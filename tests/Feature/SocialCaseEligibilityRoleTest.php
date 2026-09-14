<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\SocialCase\EligibilityAuditLog;
use App\Models\SocialCase\FamilyMember;
use App\Models\SocialCase\SocialCaseStudy;
use App\Models\User;
use App\Services\NameMatcher;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SocialCaseEligibilityRoleTest extends TestCase
{
    private array $createdUserIds = [];

    private array $createdClientIds = [];

    private array $createdCaseIds = [];

    private array $auditNames = [];

    protected function tearDown(): void
    {
        // Tests run against the live database; always clean up artifacts even on
        // assertion failures so no test residue is left behind.
        if (!empty($this->auditNames)) {
            EligibilityAuditLog::whereIn('client_name', $this->auditNames)->delete();
        }

        if (!empty($this->createdCaseIds)) {
            FamilyMember::whereIn('social_case_study_id', $this->createdCaseIds)->delete();
            \Illuminate\Support\Facades\DB::table('case_interviews')
                ->whereIn('social_case_study_id', $this->createdCaseIds)->delete();
            SocialCaseStudy::whereIn('id', $this->createdCaseIds)->delete();
        }

        if (!empty($this->createdClientIds)) {
            Client::whereIn('id', $this->createdClientIds)->delete();
        }

        if (!empty($this->createdUserIds)) {
            User::whereIn('id', $this->createdUserIds)->delete();
        }

        parent::tearDown();
    }

    private function register(?array $clientIds = [], ?array $caseIds = [], ?array $auditNames = []): void
    {
        $this->createdClientIds = array_merge($this->createdClientIds, $clientIds ?? []);
        $this->createdCaseIds = array_merge($this->createdCaseIds, $caseIds ?? []);
        $this->auditNames = array_merge($this->auditNames, $auditNames ?? []);
    }

    private function makeUser(string $role): User
    {
        $user = User::create([
            'name' => ucfirst($role),
            'email' => $role . '.' . uniqid() . '@example.com',
            'password' => Hash::make('Password123!'),
            'role' => $role,
            'phone' => '09170000000',
            'status' => 'active',
        ]);
        $this->createdUserIds[] = $user->id;

        return $user;
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
        $this->register([], [], [$clientName]);

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
        $this->register([$case->main_client_id], [$caseId]);

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
        $this->register([$case->main_client_id], [$caseId]);
    }

    public function test_store_case_rejects_a_case_that_did_not_pass_eligibility(): void
    {
        $worker = $this->makeUser('social_worker');
        $client = Client::create(['first_name' => 'Pending', 'last_name' => uniqid()]);
        $this->register([$client->id]);

        $pending = SocialCaseStudy::create([
            'main_client_id' => $client->id,
            'officer_id' => $worker->id,
            'case_number' => 'MSWD-O-2026-08-000X',
            'status' => 'Draft',
            'eligibility_status' => 'pending',
        ]);
        $this->register([], [$pending->id]);

        $this->sessionAs($worker)->postJson(route('admin.social-case.api.store'), $this->minimalStorePayload('Pending Person', $pending->id))
            ->assertStatus(403);

        // The rejected encode attempt resolves/creates a client record; register it
        // so the cleanup removes it too.
        $resolved = Client::where('first_name', 'pending')->where('last_name', 'person')->first();
        if ($resolved) {
            $this->register([$resolved->id]);
        }
    }

    public function test_submit_eligibility_cannot_bypass_six_month_rule(): void
    {
        $checker = $this->makeUser('eligibility_checker');
        $client = Client::create(['first_name' => 'Recent', 'last_name' => uniqid()]);
        $this->register([$client->id]);

        // The 6-month restriction is anchored on the case itself (latest event date).
        $case = SocialCaseStudy::create([
            'main_client_id' => $client->id,
            'officer_id' => $checker->id,
            'case_number' => 'TEST-BLK-' . uniqid(),
            'status' => 'Released',
            'released_at' => now()->subDay(),
            'created_at' => now()->subDay(),
        ]);
        $this->register([], [$case->id]);

        $name = $client->first_name . ' ' . $client->last_name;
        $this->register([], [], [$name]);

        $this->sessionAs($checker)->postJson(route('admin.social-case.api.eligibility.check'), ['client_name' => $name])
            ->assertOk()
            ->assertJsonPath('eligible', false)
            ->assertJsonPath('blocking.case_number', function ($caseNumber) {
                return $caseNumber !== null && str_contains($caseNumber, 'BLK');
            });

        $this->sessionAs($checker)->postJson(route('admin.social-case.api.eligibility.submit'), ['client_name' => $name])
            ->assertStatus(422);

        $this->assertSame(1, $client->socialCaseStudies()->count());
    }

    /**
     * Seed a realistic client ("Gerald Louis F. <Surname>") with a unique surname
     * so reorder assertions stay isolated from any pre-existing rows.
     */
    private function seedGeraldSumayloClient(): array
    {
        $surname = 'Sumaylo' . substr(strtoupper(md5(uniqid())), 0, 4);
        $client = Client::create(['first_name' => 'Gerald', 'middle_name' => 'Louis F', 'last_name' => $surname]);
        $this->register([$client->id]);

        return [$client, $surname];
    }

    public function test_eligibility_detects_reordered_spaced_and_mis_cased_names(): void
    {
        [$client, $surname] = $this->seedGeraldSumayloClient();

        // Reordered name (surname first, no comma) — the reported case
        $reordered = NameMatcher::findCandidateClients(NameMatcher::parseFullName("{$surname} Gerald Louis"));
        $this->assertTrue($reordered['exact']->contains('id', $client->id));

        // Swallowed spacing between given name parts
        $misSpaced = NameMatcher::findCandidateClients(NameMatcher::parseFullName("GeraldLouis {$surname}"));
        $this->assertTrue($misSpaced['exact']->contains('id', $client->id));

        // Random casing and extra whitespace
        $misCased = NameMatcher::findCandidateClients(NameMatcher::parseFullName(' gErAlD   LoUiS ' . strtolower($surname)));
        $this->assertTrue($misCased['exact']->contains('id', $client->id));

        // Standard order still matches exactly
        $standard = NameMatcher::findCandidateClients(NameMatcher::parseFullName("Gerald Louis {$surname}"));
        $this->assertTrue($standard['exact']->contains('id', $client->id));

        // A given-name-only overlap (middle name typed as the first name) is a
        // possible duplicate, never an exact match
        $partial = NameMatcher::findCandidateClients(NameMatcher::parseFullName("Louis {$surname}"));
        $this->assertTrue($partial['exact']->isEmpty());
        $this->assertTrue($partial['partial']->contains('id', $client->id));

        // Unrelated name does not match
        $none = NameMatcher::findCandidateClients(NameMatcher::parseFullName('Juan Dela Cruz'));
        $this->assertTrue($none['exact']->isEmpty());
        $this->assertTrue($none['partial']->isEmpty());
    }

    public function test_eligibility_check_detects_reordered_name_and_blocks_existing_case(): void
    {
        $checker = $this->makeUser('eligibility_checker');
        [$client, $surname] = $this->seedGeraldSumayloClient();

        $case = SocialCaseStudy::create([
            'main_client_id' => $client->id,
            'officer_id' => $checker->id,
            'case_number' => 'TEST-REORDER-' . uniqid(),
            'status' => 'Released',
            'released_at' => now()->subDay(),
            'created_at' => now()->subDay(),
        ]);
        $this->register([], [$case->id]);
        $name = "{$surname} Gerald Louis";
        $this->register([], [], [$name]);

        $this->sessionAs($checker)->postJson(route('admin.social-case.api.eligibility.check'), [
            'client_name' => $name,
        ])->assertOk()
            ->assertJsonPath('client_found', true)
            ->assertJsonPath('match_type', 'exact')
            ->assertJsonPath('eligible', false)
            ->assertJsonPath('blocking.case_number', $case->case_number);
    }

    public function test_submit_eligibility_with_reordered_name_does_not_create_duplicate_client(): void
    {
        $checker = $this->makeUser('eligibility_checker');
        [$client, $surname] = $this->seedGeraldSumayloClient();

        $case = SocialCaseStudy::create([
            'main_client_id' => $client->id,
            'officer_id' => $checker->id,
            'case_number' => 'TEST-REORD-SUB-' . uniqid(),
            'status' => 'Released',
            'released_at' => now()->subDay(),
            'created_at' => now()->subDay(),
        ]);
        $this->register([], [$case->id]);

        // Reordered name resolves to the existing client, which is blocked, so the
        // submit is rejected instead of creating a duplicate client record.
        $this->sessionAs($checker)->postJson(route('admin.social-case.api.eligibility.submit'), [
            'client_name' => "{$surname} Gerald Louis",
        ])->assertStatus(422);

        $this->assertSame(1, Client::whereRaw('LOWER(last_name) = ?', [strtolower($surname)])->count());
        $this->assertSame(1, $client->socialCaseStudies()->count());
    }

    public function test_store_case_with_reordered_name_links_to_existing_client(): void
    {
        $worker = $this->makeUser('social_worker');
        [$client, $surname] = $this->seedGeraldSumayloClient();

        $resp = $this->sessionAs($worker)->postJson(route('admin.social-case.api.store'), $this->minimalStorePayload("{$surname} Gerald Louis"));
        $resp->assertStatus(201);

        $caseId = $resp->json('id');
        $case = SocialCaseStudy::find($caseId);
        $this->assertSame($client->id, $case->main_client_id);
        $this->register([], [$caseId]);
        $this->assertSame(1, Client::whereRaw('LOWER(last_name) = ?', [strtolower($surname)])->count());
    }

    public function test_surname_first_name_is_stored_in_correct_columns(): void
    {
        [$client, $surname] = $this->seedGeraldSumayloClient();

        // "Sumaylo Gerald Louis F." (surname first, trailing middle initial) must
        // parse into the same components as the standard "Gerald Louis F. Sumaylo"
        // so stored client columns are never corrupted.
        $parsed = NameMatcher::parseFullName("{$surname} Gerald Louis F.");
        $this->assertSame('gerald', $parsed['first_name']);
        $this->assertSame('louis f', $parsed['middle_name']);
        $this->assertSame(strtolower($surname), $parsed['last_name']);

        // And the reordered name (with trailing initial) still resolves to the client.
        $reordered = NameMatcher::findCandidateClients(NameMatcher::parseFullName("{$surname} Gerald Louis F."));
        $this->assertTrue($reordered['exact']->contains('id', $client->id));
    }

    public function test_submit_eligibility_stores_surname_first_name_in_correct_columns(): void
    {
        $checker = $this->makeUser('eligibility_checker');
        $name = 'Sumaylo Gerald Louis F.';
        $this->register([], [], [$name, 'Gerald Louis F. Sumaylo']);

        $resp = $this->sessionAs($checker)->postJson(route('admin.social-case.api.eligibility.submit'), [
            'client_name' => $name,
        ]);
        $resp->assertStatus(201);

        // The brand-new client is created with correct first/middle/last columns.
        $client = Client::whereRaw('LOWER(last_name) = ?', ['sumaylo'])->first();
        $this->assertNotNull($client);
        $this->assertSame('gerald', $client->first_name);
        $this->assertSame('louis f', $client->middle_name);
        $this->assertSame('sumaylo', $client->last_name);
        $this->register([$client->id]);

        $caseId = $resp->json('case.id');
        $case = SocialCaseStudy::find($caseId);
        $this->assertSame($client->id, $case->main_client_id);
        $this->assertEquals('gerald', $case->intake_first_name);
        $this->assertEquals('louis f', $case->intake_middle_name);
        $this->assertEquals('sumaylo', $case->intake_last_name);
        $this->register([], [$caseId]);

        // A later standard-order check must find the same client and stay blocked
        // by the just-created handoff case.
        $checkResp = $this->sessionAs($checker)->postJson(route('admin.social-case.api.eligibility.check'), [
            'client_name' => 'Gerald Louis F. Sumaylo',
        ]);
        $checkResp->assertOk()
            ->assertJsonPath('client_found', true)
            ->assertJsonPath('eligible', false)
            ->assertJsonPath('client.id', $client->id);
    }

    public function test_family_member_relative_is_not_blocked_by_another_persons_case(): void
    {
        $checker = $this->makeUser('eligibility_checker');
        $main = Client::create(['first_name' => 'Main', 'last_name' => uniqid()]);
        $relative = Client::create(['first_name' => 'Relative', 'last_name' => uniqid()]);
        $this->register([$main->id, $relative->id]);

        $case = SocialCaseStudy::create([
            'main_client_id' => $main->id,
            'officer_id' => $checker->id,
            'case_number' => 'TEST-REL-' . uniqid(),
            'status' => 'Released',
            'released_at' => now()->subDay(),
            'created_at' => now()->subDay(),
        ]);
        $this->register([], [$case->id]);

        FamilyMember::create([
            'social_case_study_id' => $case->id,
            'person_id' => $relative->id,
            'full_name' => $relative->full_name,
            'relationship' => 'Spouse',
        ]);

        $relativeName = $relative->first_name . ' ' . $relative->last_name;
        $mainName = $main->first_name . ' ' . $main->last_name;
        $this->register([], [], [$relativeName, $mainName]);

        $this->sessionAs($checker)->postJson(route('admin.social-case.api.eligibility.check'), [
                'client_name' => $relativeName,
            ])
            ->assertOk()
            ->assertJsonPath('eligible', true);

        $this->sessionAs($checker)->postJson(route('admin.social-case.api.eligibility.check'), [
                'client_name' => $mainName,
            ])
            ->assertOk()
            ->assertJsonPath('eligible', false);

        $this->assertTrue($relative->socialCaseStudies()->where('id', $case->id)->doesntExist());
        $this->assertTrue($main->socialCaseStudies()->where('id', $case->id)->exists());
    }

    public function test_case_older_than_six_months_is_no_longer_blocking(): void
    {
        $checker = $this->makeUser('eligibility_checker');
        $client = Client::create(['first_name' => 'OldCase', 'last_name' => uniqid()]);
        $this->register([$client->id]);

        $case = SocialCaseStudy::create([
            'main_client_id' => $client->id,
            'officer_id' => $checker->id,
            'case_number' => 'TEST-OLD-' . uniqid(),
            'status' => 'Released',
            'released_at' => now()->subMonths(7),
        ]);
        $this->register([], [$case->id]);

        // created_at is not mass-assignable; shift timestamps after creation so every
        // event date on the case predates the 6-month window.
        $case->timestamps = false;
        $case->created_at = now()->subMonths(7);
        $case->save();

        $name = $client->first_name . ' ' . $client->last_name;
        $this->register([], [], [$name]);

        $this->sessionAs($checker)->postJson(route('admin.social-case.api.eligibility.check'), [
                'client_name' => $name,
            ])
            ->assertOk()
            ->assertJsonPath('eligible', true);

        $this->assertSame(0, $client->socialCaseStudies()->where('status', 'Released')->where('released_at', '>', now()->subMonths(6))->count());
    }

    public function test_admin_can_access_both_roles(): void
    {
        $admin = $this->makeUser('admin');
        $this->register([], [], ['Admin Check']);

        $this->sessionAs($admin)->get(route('admin.social-case.intake'))->assertOk();
        $this->sessionAs($admin)->postJson(route('admin.social-case.api.eligibility.check'), ['client_name' => 'Admin Check'])
            ->assertOk();
    }

    public function test_submitted_cases_page_is_encoder_only(): void
    {
        $worker = $this->makeUser('social_worker');
        $checker = $this->makeUser('eligibility_checker');

        $this->sessionAs($worker)->get(route('admin.social-case.submitted'))->assertOk();

        $this->sessionAs($checker)->get(route('admin.social-case.submitted'))->assertForbidden();
    }

    public function test_submitted_cases_page_lists_forwarded_eligible_clients(): void
    {
        $worker = $this->makeUser('social_worker');
        $checker = $this->makeUser('eligibility_checker');
        $client = Client::create(['first_name' => 'ForwardedOne', 'last_name' => uniqid()]);
        $this->register([$client->id]);

        $submitted = SocialCaseStudy::create([
            'main_client_id' => $client->id,
            'officer_id' => $worker->id,
            'case_number' => 'MSWD-O-2026-08-SUB1',
            'status' => 'Draft',
            'eligibility_status' => 'eligible',
            'eligible_by' => $checker->id,
            'eligible_at' => now(),
            'workflow_step' => 'requirements_verification',
        ]);
        $this->register([], [$submitted->id]);

        $this->sessionAs($worker)->get(route('admin.social-case.submitted'))
            ->assertOk()
            ->assertSee($client->full_name, false)
            ->assertSee('Eligibility Checker', false)
            ->assertSee('Encode');

        // Another social worker should NOT see this case
        $otherWorker = $this->makeUser('social_worker');
        $this->sessionAs($otherWorker)->get(route('admin.social-case.submitted'))
            ->assertOk()
            ->assertDontSee($client->full_name, false);
    }

    public function test_case_encoding_account_hides_disabled_navbar_buttons(): void
    {
        $worker = $this->makeUser('social_worker');

        $response = $this->sessionAs($worker)->get(route('admin.social-case.dashboard'));
        $response->assertOk();
        $response->assertDontSee('/admin/social-case/new');
        $response->assertDontSee('/admin/social-case/online-requests');
        $response->assertDontSee('Online Requests');
        $response->assertSee('/admin/social-case/submitted');
        $response->assertSee('/admin/social-case/cases');
        $response->assertSee('/admin/social-case/archive');
    }

    public function test_eligibility_checker_account_hides_disabled_navbar_buttons(): void
    {
        $checker = $this->makeUser('eligibility_checker');

        $response = $this->sessionAs($checker)->get(route('admin.social-case.dashboard'));
        $response->assertOk();
        $response->assertSee('/admin/social-case/new');
        $response->assertSee('/admin/social-case/online-requests');
        $response->assertSee('Online Requests');
        $response->assertDontSee('/admin/social-case/submitted');
        $response->assertDontSee('Submitted Cases');
        $response->assertDontSee('/admin/social-case/cases');
        $response->assertDontSee('/admin/social-case/archive');
    }

    public function test_get_encoders_returns_active_encoder_accounts(): void
    {
        $checker = $this->makeUser('eligibility_checker');
        $worker = $this->makeUser('social_worker');

        $response = $this->sessionAs($checker)->getJson(route('admin.social-case.api.encoders'));
        $response->assertOk();
        $this->assertNotEmpty($response->json());

        $workerIds = collect($response->json())->pluck('id')->all();
        $this->assertContains($worker->id, $workerIds);
    }

    public function test_submit_eligibility_assigns_chosen_encoder(): void
    {
        $checker = $this->makeUser('eligibility_checker');
        $worker = $this->makeUser('social_worker');
        $clientName = 'EncoderSelect ' . uniqid();
        $this->register([], [], [$clientName]);

        $resp = $this->sessionAs($checker)->postJson(route('admin.social-case.api.eligibility.submit'), [
            'client_name' => $clientName,
            'encoder_id'  => $worker->id,
        ]);

        $resp->assertStatus(201);
        $caseId = $resp->json('case.id');
        $this->assertNotNull($caseId);

        $case = SocialCaseStudy::find($caseId);
        $this->assertSame($worker->id, $case->officer_id);
        $this->assertSame($checker->id, $case->eligible_by);
        $this->register([$case->main_client_id], [$caseId]);
    }
}