<?php

namespace Tests\Feature;

use App\Models\Financial\OnlineFinancialIntake;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OnlineFinancialIntakeControlNumberTest extends TestCase
{
    use DatabaseTransactions;

    protected function getSampleOnlinePayload(): array
    {
        return [
            'client_type' => 'New',
            'is_client_beneficiary' => '1',
            'beneficiary_first_name' => 'Juan',
            'beneficiary_middle_name' => 'Dela',
            'beneficiary_last_name' => 'TestCruz' . time(),
            'beneficiary_birthday' => '1990-01-01',
            'beneficiary_age' => '36',
            'beneficiary_sex' => 'Male',
            'beneficiary_civil_status' => 'Single',
            'beneficiary_contact_number' => '09123456789',
            'beneficiary_street_address' => 'Unit 123 Test St',
            'beneficiary_barangay' => 'Biga I',
            'beneficiary_occupation' => 'Freelancer',
            'beneficiary_monthly_salary' => '15000',
            'beneficiary_categories' => ['PWD'],
            'beneficiary_category' => 'PWD',
            'has_representative' => '0',
            'assistance_purpose' => 'Medical Assistance Test',
            'purpose' => 'Medical Assistance Test',
            'service_provided' => 'Financial Assistance Intake',
        ];
    }

    public function test_financial_assistance_page_loads_successfully_without_control_number(): void
    {
        $response = $this->get('/financial-assistance');
        $response->assertStatus(200);
        $response->assertViewIs('financial-assistance');
        $response->assertDontSee('undefined variable $controlNumber', false);
    }

    public function test_online_submission_has_null_control_number(): void
    {
        $payload = $this->getSampleOnlinePayload();

        $response = $this->postJson('/financial-assistance', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('online_financial_intakes', [
            'beneficiary_first_name' => 'Juan',
            'beneficiary_last_name' => $payload['beneficiary_last_name'],
            'status' => 'For Review',
            'control_number' => null,
        ]);

        $online = OnlineFinancialIntake::where('beneficiary_last_name', $payload['beneficiary_last_name'])->first();
        $this->assertNotNull($online);
        $this->assertNull($online->control_number);
    }

    public function test_rejected_online_application_remains_without_control_number(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $online = OnlineFinancialIntake::create([
            'status' => 'For Review',
            'control_number' => null,
            'client_type' => 'New',
            'beneficiary_first_name' => 'Pedro',
            'beneficiary_last_name' => 'RejectTest',
            'beneficiary_street_address' => 'Sample Street 123',
            'beneficiary_barangay' => 'Biga I',
            'beneficiary_contact_number' => '09123456789',
            'beneficiary_birthday' => '1995-05-10',
            'beneficiary_age' => 30,
            'beneficiary_sex' => 'Male',
            'beneficiary_civil_status' => 'Single',
            'assistance_purpose' => 'Test Rejection',
        ]);

        $this->assertNull($online->control_number);

        $response = $this->actingAs($user)
            ->withSession(['admin_user_id' => $user->id])
            ->postJson("/admin/financial/online-intakes/{$online->id}/reject", [
                'rejection_reason' => 'Incomplete documents provided.',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'Rejected',
                ]
            ]);

        $online->refresh();
        $this->assertEquals('Rejected', $online->status);
        $this->assertNull($online->control_number);
    }

    public function test_accepting_online_application_generates_and_assigns_control_number(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $online = OnlineFinancialIntake::create([
            'status' => 'For Review',
            'control_number' => null,
            'client_type' => 'New',
            'is_client_beneficiary' => true,
            'beneficiary_first_name' => 'Clara',
            'beneficiary_last_name' => 'AcceptTest' . time(),
            'beneficiary_street_address' => '123 Biluso Proper',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_contact_number' => '09987654321',
            'beneficiary_birthday' => '1992-03-10',
            'beneficiary_age' => 33,
            'beneficiary_sex' => 'Female',
            'beneficiary_civil_status' => 'Married',
            'assistance_purpose' => 'Hospitalization Assistance',
            'date_submitted' => now(),
        ]);

        $this->assertNull($online->control_number);

        $response = $this->actingAs($user)
            ->withSession(['admin_user_id' => $user->id])
            ->postJson("/admin/financial/online-intakes/{$online->id}/accept", [
                'review_notes' => 'Verified all submitted records.',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $assignedControlNumber = $response->json('data.control_number');
        $this->assertNotNull($assignedControlNumber);
        $this->assertMatchesRegularExpression('/^MSWDO-\d{4}-\d{5}$/', $assignedControlNumber);

        // Check Online record has updated status and control_number
        $online->refresh();
        $this->assertEquals('Accepted', $online->status);
        $this->assertEquals($assignedControlNumber, $online->control_number);
        $this->assertNotNull($online->beneficiary_intake_id);

        // Check regular BeneficiaryIntake record was created with identical control_number
        $intake = BeneficiaryIntake::find($online->beneficiary_intake_id);
        $this->assertNotNull($intake);
        $this->assertEquals($assignedControlNumber, $intake->control_number);
        $this->assertEquals('Clara', $intake->beneficiary_first_name);

        // Trying to accept again should be rejected and not change control number
        $secondAcceptResponse = $this->actingAs($user)
            ->withSession(['admin_user_id' => $user->id])
            ->postJson("/admin/financial/online-intakes/{$online->id}/accept");

        $secondAcceptResponse->assertStatus(422);
        $online->refresh();
        $this->assertEquals($assignedControlNumber, $online->control_number);
    }
}
