<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\OnlineRequest;
use App\Models\SocialCase\SocialCaseStudy;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SocialCaseNotificationTest extends TestCase
{
    private array $createdUserIds = [];
    private array $createdClientIds = [];
    private array $createdCaseIds = [];
    private array $createdOnlineRequestIds = [];

    protected function tearDown(): void
    {
        if (!empty($this->createdCaseIds)) {
            SocialCaseStudy::whereIn('id', $this->createdCaseIds)->delete();
        }
        if (!empty($this->createdOnlineRequestIds)) {
            OnlineRequest::whereIn('id', $this->createdOnlineRequestIds)->delete();
        }
        if (!empty($this->createdClientIds)) {
            Client::whereIn('id', $this->createdClientIds)->delete();
        }
        if (!empty($this->createdUserIds)) {
            User::whereIn('id', $this->createdUserIds)->delete();
        }

        parent::tearDown();
    }

    private function createUser(string $role): User
    {
        $user = User::create([
            'name' => ucfirst($role) . ' ' . uniqid(),
            'email' => $role . '.' . uniqid() . '@example.com',
            'password' => Hash::make('Password123!'),
            'role' => $role,
            'phone' => '09170000000',
            'status' => 'active',
        ]);
        $this->createdUserIds[] = $user->id;
        return $user;
    }

    public function test_can_fetch_notifications_for_forwarded_walkin_and_online_requests(): void
    {
        $checker = $this->createUser('eligibility_checker');
        $encoder = $this->createUser('social_worker');

        // 1. Create a forwarded walk-in client
        $client = Client::create([
            'first_name' => 'NotificationTest',
            'last_name' => 'WalkinClient',
        ]);
        $this->createdClientIds[] = $client->id;

        $case = SocialCaseStudy::create([
            'main_client_id' => $client->id,
            'first_name' => $client->first_name,
            'last_name' => $client->last_name,
            'officer_id' => $encoder->id,
            'case_number' => 'NOTIF-TEST-' . uniqid(),
            'date_processed' => now()->toDateString(),
            'status' => 'Draft',
            'eligibility_status' => 'eligible',
            'eligible_by' => $checker->id,
            'eligible_at' => now(),
            'workflow_step' => 'requirements_verification',
            'document_ref_number' => 99999,
        ]);
        $this->createdCaseIds[] = $case->id;

        // 2. Create an accepted online request
        $onlineRequest = OnlineRequest::create([
            'request_for' => 'myself',
            'first_name' => 'OnlineNotif',
            'last_name' => 'Applicant',
            'dob' => '1995-05-15',
            'email' => 'online.notif@example.com',
            'contact_number' => '09123456789',
            'service_type' => 'social_case_study',
            'assistance_type' => 'medical',
            'barangay' => 'ACACIA',
            'status' => 'approved',
            'situation' => 'Online request approved for testing',
        ]);
        $this->createdOnlineRequestIds[] = $onlineRequest->id;

        // Fetch notifications as encoder
        $response = $this->withSession([
            'admin_user_id' => $encoder->id,
            'admin_user_name' => $encoder->name,
            'admin_user_role' => 'social_worker',
        ])->getJson('/admin/social-case/api/notifications');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json();
        $this->assertGreaterThanOrEqual(2, $data['total_count']);
        $this->assertGreaterThanOrEqual(2, $data['unread_count']);

        $types = array_column($data['notifications'], 'type');
        $this->assertContains('client_eligibility', $types);
        $this->assertContains('online_request', $types);

        // Verify mark-read endpoint
        $markReadResponse = $this->withSession([
            'admin_user_id' => $encoder->id,
            'admin_user_name' => $encoder->name,
            'admin_user_role' => 'social_worker',
        ])->postJson('/admin/social-case/api/notifications/mark-read');

        $markReadResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        // Subsequent getNotifications in the same session should have 0 unread
        $afterReadResponse = $this->withSession([
            'admin_user_id' => $encoder->id,
            'admin_user_name' => $encoder->name,
            'admin_user_role' => 'social_worker',
            'social_case_notifications_read_at' => now()->addSecond()->toIso8601String(),
        ])->getJson('/admin/social-case/api/notifications');

        $afterReadResponse->assertStatus(200);
        $afterData = $afterReadResponse->json();
        $this->assertEquals(0, $afterData['unread_count']);
    }

    public function test_eligibility_checker_receives_notification_when_new_online_request_submitted(): void
    {
        $checker = $this->createUser('eligibility_checker');

        // Create a new pending online request as submitted from /service-request
        $pendingRequest = OnlineRequest::create([
            'request_for' => 'myself',
            'first_name' => 'PendingCitizen',
            'last_name' => 'Applicant',
            'dob' => '1990-01-01',
            'email' => 'pending.citizen@example.com',
            'contact_number' => '09199999999',
            'service_type' => 'social_case_study',
            'assistance_type' => 'medical',
            'barangay' => 'LALAAN_I',
            'status' => 'pending',
            'situation' => 'Needs urgent medical assistance',
        ]);
        $this->createdOnlineRequestIds[] = $pendingRequest->id;

        // Fetch notifications as eligibility checker
        $response = $this->withSession([
            'admin_user_id' => $checker->id,
            'admin_user_name' => $checker->name,
            'admin_user_role' => 'eligibility_checker',
        ])->getJson('/admin/social-case/api/notifications');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json();
        $this->assertGreaterThanOrEqual(1, $data['total_count']);
        $this->assertGreaterThanOrEqual(1, $data['unread_count']);

        $pendingItem = collect($data['notifications'])->firstWhere('id', 'pending_online_' . $pendingRequest->id);
        $this->assertNotNull($pendingItem);
        $this->assertEquals('pending_online_request', $pendingItem['type']);
        $this->assertEquals('New Online Request Submitted', $pendingItem['title']);
        $this->assertEquals('PendingCitizen Applicant', $pendingItem['client_name']);
        $this->assertEquals('New Online Request', $pendingItem['badge_text']);
        $this->assertEquals(route('admin.social-case.online-requests'), $pendingItem['url']);
    }
}
