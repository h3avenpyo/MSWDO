<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\SocialCase\AssistanceRecord;
use App\Models\SocialCase\EligibilityAuditLog;
use App\Models\SocialCase\SocialCaseStudy;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SocialCaseEligibilityRoleTest extends TestCase
{
    private function makeUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $role . '.' . uniqid() . '@example.com',
            'password' => Hash::make('Password123!'),
            'role' => $role,
            'phone' => '09170000000',
            'status' => 'active',
        ]);
    }

    private function sessionAs(User $user): self
    {
        return $this->withSession([
            'admin_user_id' => $user->id,
            'admin_user_name' => $user->name,
            'admin_user_role' => $user->role->value,
        ]);
    }

    private function minimalStorePayload(string $name, ?int $caseId = null): array
    {
        return [
            'case_id' => $caseId,
            'control_no' => 'TEST-' . substr(md5($name . microtime()), 0, 8),
            'status' => 'Draft',
            'client' => [
                'name' => $name,
                'age' => 35,
                'sex' => 'Male',
                'address' => 'Barangay Uno',
                'birthdate' => '1990-01-01',
                'birthplace' => 'Manila',
                'contact' => '09171112233',
            ],
            'household' => [
                ['name' => $name, 'relationship' => 'Self', 'age' => 35, 'occupation' => 'Driver', 'income' => '5000'],
            ],
            'interview' => [
                'problem_presented' => 'Test problem',
                'report_date' => now()->toDateString(),
            ],
            'signers' => [
                'prepared_by_name' => 'Preparer',
                'prepared_by_title' => 'MSWDO Staff',
                'noted_by_name' => 'Noter',
                'noted_by_title' => 'MSWDO Head',
            ],
            'purpose' => 'Medical Assistance',
            'agencies' => ['MSWDO'],
            'requirements' => [
                ['name' => 'Barangay Certificate', 'submitted' => true],
            ],
        ];
    }

    public function test_eligibility_checker_can_check_and_submit_but_cannot_encode(): void
    {
        $checker = $this->makeUser('eligibility_checker');
        $clientName = 'CheckTest ' . uniqid();

        // Can run the server-side eligibility check
        $this->sessionAs($checker)->postJson(route('admin.social-case.api.eligibility.check'), [
            'client_name' => $clientName,
        ])->assertOk()->assertJsonPath('eligible', true);

        // Can forward an eligible client for encoding
        $submitResp = $this->sessionAs($checker)->postJson(route('admin.social-case.api.eligibility.submit'), [
            'client_name' => $clientName,
        ])->assertStatus(201)->assertJsonPath('eligible', true);

        $caseId = $submitResp->json('case.id');

        $case = SocialCaseStudy::find($caseId);
        $this->assertSame('eligible', $case->eligibility_status);
        $this->assertSame($checker->id, $case->eligible_by);
        $this->assertNotNull($case->eligible_at);
        $this->assertSame('Draft', $case->status);
        $this->assertSame('requirements_verification', $case->workflow_step);
        $this->assertNull($case->encoded_by);

        // Cannot access the encoder-only intake page
        $this->sessionAs($checker)->get(route('admin.social-case.intake'))->assertForbidden();

        // Cannot create, update, or delete cases
        $this->sessionAs($checker)->postJson(route('admin.social-case.api.store'), $this->minimalStorePayload('NoEncode'))->assertForbidden();
        $this->sessionAs($checker)->putJson(route('admin.social-case.api.update', $caseId), ['status' => 'Review'])->assertForbidden();
        $this->sessionAs($checker)->deleteJson(route('admin.social-case.api.delete', $caseId))->assertForbidden();

        // Audit log was written for the check
        $this->assertSame(
            1,
            EligibilityAuditLog::where('client_name', $clientName)->count()
        );

        $case->delete();
        Client::where('id', $case->client_id)->delete();
        EligibilityAuditLog::where('client_name', $clientName)->delete();
        $checker->delete();
    }

    public function test_social_worker_can_encode_but_cannot_run_eligibility_checks(): void
    {
        $worker = $this->makeUser('social_worker');
        $clientName = 'EncTest ' . uniqid();

        // Can open the intake page
        $this->sessionAs($worker)->get(route('admin.social-case.intake'))->assertOk();

        // Cannot run or submit eligibility
        $this->sessionAs($worker)->postJson(route('admin.social-case.api.eligibility.check'), ['client_name' => $clientName])->assertForbidden();
        $this->sessionAs($worker)->postJson(route('admin.social-case.api.eligibility.submit'), ['client_name' => $clientName])->assertForbidden();

        // Can create a new case (gets eligibility_status = eligible automatically)
        $resp = $this->sessionAs($worker)->postJson(route('admin.social-case.api.store'), $this->minimalStorePayload($clientName));
        $resp->assertStatus(201);
        $caseId = $resp->json('id');
        $case = SocialCaseStudy::find($caseId);
        $this->assertSame('eligible', $case->eligibility_status);
        $this->assertSame($worker->id, $case->encoded_by);

        $case->delete();
        Client::where('id', $case->client_id)->delete();
        $worker->delete();
    }

    public function test_store_case_rejects_a_case_that_did_not_pass_eligibility(): void
    {
        $worker = $this->makeUser('social_worker');
        $client = Client::create(['first_name' => 'Pending', 'last_name' => uniqid()]);

        $pending = SocialCaseStudy::create([
            'client_id' => $client->id,
            'officer_id' => $worker->id,
            'case_number' => 'MSWD-O-2026-08-000X',
            'status' => 'Draft',
            'eligibility_status' => 'pending',
        ]);

        $this->sessionAs($worker)->postJson(route('admin.social-case.api.store'), $this->minimalStorePayload('Pending Person', $pending->id))
            ->assertStatus(403);

        $pending->delete();
        $client->delete();
        $worker->delete();
    }

    public function test_submit_eligibility_cannot_bypass_six_month_rule(): void
    {
        $checker = $this->makeUser('eligibility_checker');
        $client = Client::create(['first_name' => 'Recent', 'last_name' => uniqid()]);

        AssistanceRecord::create([
            'client_id' => $client->id,
            'assistance_type' => 'Medical Assistance',
            'status' => 'Released',
            'release_date' => now()->subDay()->toDateString(),
        ]);

        $name = $client->first_name . ' ' . $client->last_name;

        $this->sessionAs($checker)->postJson(route('admin.social-case.api.eligibility.check'), ['client_name' => $name])
            ->assertOk()
            ->assertJsonPath('eligible', false);

        $this->sessionAs($checker)->postJson(route('admin.social-case.api.eligibility.submit'), ['client_name' => $name])
            ->assertStatus(422);

        $this->assertSame(0, $client->socialCaseStudies()->count());

        $client->assistanceRecords()->delete();
        $client->delete();
        $checker->delete();
    }

    public function test_admin_can_access_both_roles(): void
    {
        $admin = $this->makeUser('admin');

        $this->sessionAs($admin)->get(route('admin.social-case.intake'))->assertOk();
        $this->sessionAs($admin)->postJson(route('admin.social-case.api.eligibility.check'), ['client_name' => 'Admin Check'])
            ->assertOk();

        $admin->delete();
    }

    public function test_submitted_cases_page_is_encoder_only(): void
    {
        $worker = $this->makeUser('social_worker');
        $checker = $this->makeUser('eligibility_checker');

        $this->sessionAs($worker)->get(route('admin.social-case.submitted'))->assertOk();

        $this->sessionAs($checker)->get(route('admin.social-case.submitted'))->assertForbidden();

        $worker->delete();
        $checker->delete();
    }

    public function test_submitted_cases_page_lists_forwarded_eligible_clients(): void
    {
        $worker = $this->makeUser('social_worker');
        $checker = $this->makeUser('eligibility_checker');
        $client = Client::create(['first_name' => 'ForwardedOne', 'last_name' => uniqid()]);

        $submitted = SocialCaseStudy::create([
            'client_id' => $client->id,
            'officer_id' => $checker->id,
            'case_number' => 'MSWD-O-2026-08-SUB1',
            'status' => 'Draft',
            'eligibility_status' => 'eligible',
            'eligible_by' => $checker->id,
            'eligible_at' => now(),
            'workflow_step' => 'requirements_verification',
        ]);

        $this->sessionAs($worker)->get(route('admin.social-case.submitted'))
            ->assertOk()
            ->assertSee($client->full_name, false)
            ->assertSee('Eligibility Checker', false)
            ->assertSee('Encode');

        $submitted->delete();
        $client->delete();
        $worker->delete();
        $checker->delete();
    }
}