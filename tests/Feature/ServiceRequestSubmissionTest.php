<?php

namespace Tests\Feature;

use App\Models\OnlineRequest;
use App\Models\OnlineRequestAttachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceRequestSubmissionTest extends TestCase
{
    public function test_can_submit_social_case_study_service_request(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('medical_certificate.pdf', 500, 'application/pdf');

        $payload = [
            'request_for' => 'myself',
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'dob' => '1995-05-15',
            'barangay' => 'ACACIA',
            'contact_number' => '09123456789',
            'email' => 'maria.santos@example.com',
            'address' => 'Block 1 Lot 2, Acacia',
            'service_type' => 'social_case_study',
            'assistance_type' => 'medical',
            'situation' => 'Requesting social case study for medical financial assistance from provincial office.',
            'documents' => [$file],
        ];

        $response = $this->postJson('/service-request', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Service request submitted successfully',
            ]);

        $this->assertDatabaseHas('online_requests', [
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'service_type' => 'social_case_study',
            'assistance_type' => 'medical',
            'status' => 'pending',
        ]);

        $onlineRequest = OnlineRequest::where('email', 'maria.santos@example.com')->first();
        $this->assertNotNull($onlineRequest);

        $this->assertDatabaseHas('online_request_attachments', [
            'online_request_id' => $onlineRequest->id,
            'file_name' => 'medical_certificate.pdf',
        ]);
    }

    public function test_can_submit_financial_assistance_service_request(): void
    {
        $payload = [
            'request_for' => 'child',
            'first_name' => 'Pedro',
            'last_name' => 'Penduko',
            'dob' => '2010-02-20',
            'barangay' => 'BALITE I',
            'contact_number' => '09987654321',
            'email' => 'pedro@example.com',
            'address' => 'Balite I, Silang',
            'service_type' => 'financial_assistance',
            'assistance_type' => 'educational',
            'situation' => 'Educational assistance for school supplies and tuition fees.',
        ];

        $response = $this->postJson('/service-request', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Service request submitted successfully',
            ]);

        $this->assertDatabaseHas('online_requests', [
            'first_name' => 'Pedro',
            'last_name' => 'Penduko',
            'service_type' => 'financial_assistance',
            'assistance_type' => 'educational',
            'status' => 'pending',
        ]);
    }

    public function test_validation_fails_for_invalid_data(): void
    {
        $payload = [
            'request_for' => 'invalid_type',
            'first_name' => '',
            'service_type' => 'social_case_study',
        ];

        $response = $this->postJson('/service-request', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_can_fetch_redesigned_online_request_modal_details(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Checker Test',
            'email' => 'checker.' . uniqid() . '@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('Password123!'),
            'role' => 'eligibility_checker',
            'phone' => '09170000000',
            'status' => 'active',
        ]);

        $request = OnlineRequest::create([
            'request_for' => 'myself',
            'first_name' => 'Jose',
            'last_name' => 'Rizal',
            'dob' => '1990-06-19',
            'barangay' => 'ACACIA',
            'contact_number' => '09123456789',
            'email' => 'jose.rizal@example.com',
            'address' => 'Calamba St, Acacia',
            'service_type' => 'social_case_study',
            'assistance_type' => 'medical',
            'situation' => 'Needs medical case study for surgery assistance.',
            'status' => 'pending',
        ]);

        $attachment = OnlineRequestAttachment::create([
            'online_request_id' => $request->id,
            'file_name' => 'Hospital_Bill.pdf',
            'file_path' => 'online-request-attachments/test_bill.pdf',
            'file_type' => 'application/pdf',
            'file_size' => 204800,
        ]);

        $response = $this->withSession([
            'admin_user_id' => $user->id,
            'admin_user_name' => $user->name,
            'admin_user_role' => 'eligibility_checker',
        ])->getJson("/admin/social-case/online-requests/{$request->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'reference_no',
                'first_name',
                'last_name',
                'full_name',
                'dob',
                'age',
                'request_for',
                'email',
                'contact_number',
                'address',
                'barangay',
                'service_type',
                'assistance_type',
                'status',
                'raw_status',
                'created_at',
                'created_at_human',
                'situation',
                'attachments',
                'attachments_count',
                'warning_existing',
                'warning_recent',
            ]);

        $data = $response->json();
        $this->assertEquals('Jose Rizal', $data['full_name']);
        $this->assertEquals('Myself (Ako)', $data['request_for']);
        $this->assertEquals('Calamba St, Acacia', $data['address']);
        $this->assertCount(1, $data['attachments']);
        $this->assertEquals('Hospital_Bill.pdf', $data['attachments'][0]['file_name']);
        $this->assertTrue($data['attachments'][0]['is_pdf']);

        // Clean up
        $attachment->delete();
        $request->delete();
        $user->delete();
    }
}
