<?php

namespace Tests\Feature;

use App\Models\Financial\OnlineFinancialIntake;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use App\Services\Financial\FinancialControlNumberService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FinancialControlNumberFormatTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test the format structure: MSWDO-YYYY-MM-0000.
     */
    public function test_format_matches_required_specification(): void
    {
        $testDate = Carbon::create(2026, 7, 15);
        $preview = FinancialControlNumberService::previewNext($testDate);

        $this->assertMatchesRegularExpression('/^MSWDO-2026-07-\d{4}$/', $preview);
        $this->assertTrue(FinancialControlNumberService::isValidFormat($preview));
        $this->assertTrue(FinancialControlNumberService::isValidFormat('MSWDO-2026-07-0001'));
        $this->assertFalse(FinancialControlNumberService::isValidFormat('MSWDO-2026-00001'));
        $this->assertFalse(FinancialControlNumberService::isValidFormat('INVALID-FORMAT'));
    }

    /**
     * Test that sequence resets to 0001 for each new month.
     */
    public function test_sequence_resets_to_0001_at_start_of_new_month(): void
    {
        // Month 1 (e.g. November 2026)
        $novDate = Carbon::create(2026, 11, 1);
        $novCtrl = FinancialControlNumberService::generate($novDate);
        $this->assertEquals('MSWDO-2026-11-0001', $novCtrl);

        // Create dummy record using this number
        BeneficiaryIntake::create([
            'control_number' => $novCtrl,
            'client_type' => 'New',
            'beneficiary_first_name' => 'NovUser',
            'beneficiary_last_name' => 'Tester',
            'beneficiary_street_address' => 'Sample St',
            'beneficiary_barangay' => 'Biga I',
            'date_processed' => $novDate->toDateString(),
            'service_provided' => 'Financial Assistance Intake',
            'purpose' => 'Medical',
        ]);

        // Second generation in same month must be 0002
        $novCtrl2 = FinancialControlNumberService::generate($novDate);
        $this->assertEquals('MSWDO-2026-11-0002', $novCtrl2);

        // Next month (e.g. December 2026) must reset to 0001
        $decDate = Carbon::create(2026, 12, 1);
        $decCtrl = FinancialControlNumberService::generate($decDate);
        $this->assertEquals('MSWDO-2026-12-0001', $decCtrl);
    }

    /**
     * Test that Step 2 reuses the Step 1 Control Number and does not generate a separate one.
     */
    public function test_step2_reuses_step1_control_number(): void
    {
        $step1Ctrl = FinancialControlNumberService::generate();
        $this->assertMatchesRegularExpression('/^MSWDO-\d{4}-\d{2}-\d{4}$/', $step1Ctrl);

        $intake = BeneficiaryIntake::create([
            'control_number' => $step1Ctrl,
            'client_type' => 'New',
            'beneficiary_first_name' => 'Maria',
            'beneficiary_last_name' => 'Step2Test',
            'beneficiary_street_address' => 'Poblacion',
            'beneficiary_barangay' => 'Biga I',
            'date_processed' => now()->toDateString(),
            'service_provided' => 'Financial Assistance Intake',
            'purpose' => 'Medical Assistance',
            'recommended_amount' => 2500,
            'claim_status' => 'Pending',
        ]);

        $step2User = User::factory()->create([
            'role' => 'financialstep2',
        ]);

        // Step 2 All Intakes view
        $response = $this->actingAs($step2User)
            ->withSession([
                'admin_user_id' => $step2User->id,
                'admin_user_role' => 'financialstep2',
                'financial_step2_authenticated' => true,
            ])
            ->get(route('admin.financial.financialstep2.all-intakes'));

        $response->assertStatus(200);
        $response->assertSee($step1Ctrl);

        // Step 2 Masterlist view
        $response2 = $this->actingAs($step2User)
            ->withSession([
                'admin_user_id' => $step2User->id,
                'admin_user_role' => 'financialstep2',
                'financial_step2_authenticated' => true,
            ])
            ->get(route('admin.financial.financialstep2'));

        $response2->assertStatus(200);
        $response2->assertSee($step1Ctrl);

        // Verify intake record control number remains identical
        $intake->refresh();
        $this->assertEquals($step1Ctrl, $intake->control_number);
    }

    /**
     * Test accepting an online application transfers the newly formatted Control Number to Step 1.
     */
    public function test_online_application_acceptance_generates_new_format(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $online = OnlineFinancialIntake::create([
            'status' => 'For Review',
            'control_number' => null,
            'client_type' => 'New',
            'beneficiary_first_name' => 'Elena',
            'beneficiary_last_name' => 'OnlineCtrlTest',
            'beneficiary_street_address' => 'Biluso St',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_contact_number' => '09123456789',
            'beneficiary_birthday' => '1993-08-14',
            'beneficiary_age' => 33,
            'beneficiary_sex' => 'Female',
            'beneficiary_civil_status' => 'Single',
            'assistance_purpose' => 'Hospitalization Assistance',
            'date_submitted' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['admin_user_id' => $admin->id])
            ->postJson("/admin/financial/online-intakes/{$online->id}/accept", [
                'review_notes' => 'Online application verified and accepted.',
            ]);

        $response->assertStatus(200);
        $assignedCtrl = $response->json('data.control_number');
        $this->assertNotNull($assignedCtrl);
        $this->assertMatchesRegularExpression('/^MSWDO-\d{4}-\d{2}-\d{4}$/', $assignedCtrl);

        $online->refresh();
        $this->assertEquals('Accepted', $online->status);
        $this->assertEquals($assignedCtrl, $online->control_number);

        $createdIntake = BeneficiaryIntake::find($online->beneficiary_intake_id);
        $this->assertNotNull($createdIntake);
        $this->assertEquals($assignedCtrl, $createdIntake->control_number);
    }
}
