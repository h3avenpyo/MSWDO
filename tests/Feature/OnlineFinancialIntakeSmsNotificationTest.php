<?php

namespace Tests\Feature;

use App\Models\Financial\FinancialAssistanceMessage;
use App\Models\Financial\OnlineFinancialIntake;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use App\Services\Financial\OnlineFinancialNotificationService;
use App\Services\Sms\LogSmsProvider;
use App\Services\Sms\SmsProviderInterface;
use App\Services\Sms\SmsService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OnlineFinancialIntakeSmsNotificationTest extends TestCase
{
    use DatabaseTransactions;

    protected OnlineFinancialNotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $smsService = new SmsService(new LogSmsProvider());
        $this->notificationService = new OnlineFinancialNotificationService($smsService);
    }

    public function test_approved_online_intake_triggers_exact_approved_tagalog_sms(): void
    {
        $app = OnlineFinancialIntake::create([
            'status' => 'Accepted',
            'date_submitted' => now(),
            'beneficiary_first_name' => 'MARIA',
            'beneficiary_last_name' => 'SANTOS',
            'beneficiary_street_address' => 'PUROK 1',
            'beneficiary_barangay' => 'Biga I',
            'beneficiary_contact_number' => '09171234567',
            'beneficiary_birthday' => '1990-05-15',
            'beneficiary_age' => 36,
            'beneficiary_sex' => 'FEMALE',
            'beneficiary_civil_status' => 'SINGLE',
            'has_representative' => false,
        ]);

        $res = $this->notificationService->notifyApproval($app);

        $this->assertTrue($res['success']);
        $app->refresh();
        $this->assertNotNull($app->approval_sms_sent_at);
        $this->assertEquals('Sent', $app->sms_status);

        $msg = FinancialAssistanceMessage::where('online_intake_id', $app->id)->first();
        $this->assertNotNull($msg);
        $this->assertEquals('Online Application Approved', $msg->message_type);
        $this->assertEquals('+639171234567', $msg->recipient_contact_number);

        $expected = "Magandang araw, MARIA SANTOS! Ang inyong online application para sa Financial Assistance ng MSWDO ay naaprubahan na para sa susunod na hakbang. Maaari na kayong maghanda at magsumite ng mga kinakailangang dokumento para sa beripikasyon. Mangyaring makipag-ugnayan sa tanggapan ng MSWDO tungkol sa mga kinakailangan. Maraming salamat.";
        $this->assertEquals(trim($expected), trim($msg->message_body));
    }

    public function test_rejected_online_intake_triggers_exact_rejected_tagalog_sms(): void
    {
        $app = OnlineFinancialIntake::create([
            'status' => 'Rejected',
            'date_submitted' => now(),
            'beneficiary_first_name' => 'JUAN',
            'beneficiary_last_name' => 'DELA CRUZ',
            'beneficiary_street_address' => 'PUROK 2',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_contact_number' => '09189876543',
            'beneficiary_birthday' => '1985-02-10',
            'beneficiary_age' => 41,
            'beneficiary_sex' => 'MALE',
            'beneficiary_civil_status' => 'MARRIED',
            'has_representative' => false,
            'rejection_reason' => 'HINDI RESIDENTE NG BAYAN NG SILANG',
        ]);

        $res = $this->notificationService->notifyRejection($app);

        $this->assertTrue($res['success']);
        $app->refresh();
        $this->assertNotNull($app->rejection_sms_sent_at);
        $this->assertEquals('Sent', $app->sms_status);

        $msg = FinancialAssistanceMessage::where('online_intake_id', $app->id)->first();
        $this->assertNotNull($msg);
        $this->assertEquals('Online Application Rejected', $msg->message_type);
        $this->assertEquals('+639189876543', $msg->recipient_contact_number);

        $expected = "Magandang araw, JUAN DELA CRUZ. Ikinalulungkot naming ipaalam na hindi naaprubahan ang inyong online application para sa Financial Assistance ng MSWDO. Para sa karagdagang impormasyon tungkol sa dahilan ng pagtanggi at sa mga susunod na hakbang na maaari ninyong gawin, mangyaring makipag-ugnayan sa tanggapan ng MSWDO. Maraming salamat sa inyong pag-unawa.";
        $this->assertEquals(trim($expected), trim($msg->message_body));
    }

    public function test_representative_name_and_contact_used_when_has_representative(): void
    {
        $app = OnlineFinancialIntake::create([
            'status' => 'Accepted',
            'date_submitted' => now(),
            'beneficiary_first_name' => 'LOLITA',
            'beneficiary_last_name' => 'REYES',
            'beneficiary_street_address' => 'PUROK 3',
            'beneficiary_barangay' => 'Acacia',
            'beneficiary_contact_number' => '09051112222',
            'beneficiary_birthday' => '1945-01-01',
            'beneficiary_age' => 81,
            'beneficiary_sex' => 'FEMALE',
            'beneficiary_civil_status' => 'WIDOWED',
            'has_representative' => true,
            'rep_first_name' => 'CARLO',
            'rep_last_name' => 'REYES',
            'rep_contact_number' => '09223334444',
            'rep_relationship' => 'ANAK',
        ]);

        $this->assertEquals('CARLO REYES', $app->applicant_name);
        $this->assertEquals('09223334444', $app->applicant_contact_number);

        $this->notificationService->notifyApproval($app);
        $msg = FinancialAssistanceMessage::where('online_intake_id', $app->id)->first();

        $this->assertEquals('CARLO REYES', $msg->recipient_name);
        $this->assertEquals('+639223334444', $msg->recipient_contact_number);
        $this->assertStringContainsString('Magandang araw, CARLO REYES!', $msg->message_body);
    }

    public function test_pending_applications_do_not_trigger_sms(): void
    {
        $app = OnlineFinancialIntake::create([
            'status' => 'For Review',
            'date_submitted' => now(),
            'beneficiary_first_name' => 'PEDRO',
            'beneficiary_last_name' => 'PENDUKO',
            'beneficiary_street_address' => 'PUROK 4',
            'beneficiary_barangay' => 'Lucsuhin',
            'beneficiary_contact_number' => '09195556666',
            'beneficiary_birthday' => '1995-12-12',
            'beneficiary_age' => 31,
            'beneficiary_sex' => 'MALE',
            'beneficiary_civil_status' => 'SINGLE',
            'has_representative' => false,
        ]);

        $resApproval = $this->notificationService->notifyApproval($app);
        $this->assertFalse($resApproval['success']);
        $this->assertTrue($resApproval['skipped'] ?? false);

        $resRejection = $this->notificationService->notifyRejection($app);
        $this->assertFalse($resRejection['success']);
        $this->assertTrue($resRejection['skipped'] ?? false);

        $this->assertEquals(0, FinancialAssistanceMessage::where('online_intake_id', $app->id)->count());
    }

    public function test_duplicate_sms_prevented_on_repeated_requests(): void
    {
        $app = OnlineFinancialIntake::create([
            'status' => 'Accepted',
            'date_submitted' => now(),
            'beneficiary_first_name' => 'ROSE',
            'beneficiary_last_name' => 'MANALO',
            'beneficiary_street_address' => 'PUROK 5',
            'beneficiary_barangay' => 'Kaong',
            'beneficiary_contact_number' => '09170001111',
            'beneficiary_birthday' => '1992-03-03',
            'beneficiary_age' => 34,
            'beneficiary_sex' => 'FEMALE',
            'beneficiary_civil_status' => 'SINGLE',
            'has_representative' => false,
        ]);

        $firstRes = $this->notificationService->notifyApproval($app);
        $this->assertTrue($firstRes['success']);

        $secondRes = $this->notificationService->notifyApproval($app);
        $this->assertTrue($secondRes['success']);
        $this->assertTrue($secondRes['already_sent']);

        $this->assertEquals(1, FinancialAssistanceMessage::where('online_intake_id', $app->id)->count());
    }

    public function test_invalid_or_missing_contact_number_handled_safely(): void
    {
        $app = OnlineFinancialIntake::create([
            'status' => 'Accepted',
            'date_submitted' => now(),
            'beneficiary_first_name' => 'ANA',
            'beneficiary_last_name' => 'LOPEZ',
            'beneficiary_street_address' => 'PUROK 6',
            'beneficiary_barangay' => 'Tartaria',
            'beneficiary_contact_number' => '12345',
            'beneficiary_birthday' => '2000-01-01',
            'beneficiary_age' => 26,
            'beneficiary_sex' => 'FEMALE',
            'beneficiary_civil_status' => 'SINGLE',
            'has_representative' => false,
        ]);

        $res = $this->notificationService->notifyApproval($app);
        $this->assertFalse($res['success']);

        $app->refresh();
        $this->assertEquals('Accepted', $app->status);
        $this->assertEquals('Failed', $app->sms_status);

        $msg = FinancialAssistanceMessage::where('online_intake_id', $app->id)->first();
        $this->assertNotNull($msg);
        $this->assertEquals('Failed', $msg->status);
    }

    public function test_provider_failure_does_not_undo_status_change(): void
    {
        $failingProvider = new class implements SmsProviderInterface {
            public function send(string $to, string $message, array $options = []): array {
                return [
                    'success' => false,
                    'reference_id' => null,
                    'error' => 'Simulated SMS Gateway 503 Service Unavailable',
                    'data' => null,
                ];
            }
        };

        $failingService = new OnlineFinancialNotificationService(new SmsService($failingProvider));

        $app = OnlineFinancialIntake::create([
            'status' => 'Rejected',
            'date_submitted' => now(),
            'beneficiary_first_name' => 'ROBERTO',
            'beneficiary_last_name' => 'GARCIA',
            'beneficiary_street_address' => 'PUROK 7',
            'beneficiary_barangay' => 'Tibig',
            'beneficiary_contact_number' => '09281234567',
            'beneficiary_birthday' => '1970-07-07',
            'beneficiary_age' => 56,
            'beneficiary_sex' => 'MALE',
            'beneficiary_civil_status' => 'MARRIED',
            'has_representative' => false,
            'rejection_reason' => 'DUPLIKASYON',
        ]);

        $res = $failingService->notifyRejection($app);
        $this->assertFalse($res['success']);

        $app->refresh();
        $this->assertEquals('Rejected', $app->status);
        $this->assertEquals('Failed', $app->sms_status);
        $this->assertStringContainsString('503 Service Unavailable', $app->sms_error);

        // Retry with working service succeeds
        $retryRes = $this->notificationService->retryNotification($app);
        $this->assertTrue($retryRes['success']);

        $app->refresh();
        $this->assertEquals('Sent', $app->sms_status);
        $this->assertNotNull($app->rejection_sms_sent_at);
    }
}
