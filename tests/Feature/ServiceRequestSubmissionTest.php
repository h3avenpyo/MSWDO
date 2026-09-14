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
}
