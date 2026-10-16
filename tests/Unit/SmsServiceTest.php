<?php

namespace Tests\Unit;

use App\Services\Sms\LogSmsProvider;
use App\Services\Sms\SmsProviderInterface;
use App\Services\Sms\SmsService;
use Tests\TestCase;

class SmsServiceTest extends TestCase
{
    /**
     * Test local 09XXXXXXXXX format automatically converts to +63XXXXXXXXXX.
     */
    public function test_converts_local_09_number_to_philippine_international_format(): void
    {
        $this->assertEquals('+639171234567', SmsService::formatToPhilippineInternational('09171234567'));
        $this->assertEquals('+639181112222', SmsService::normalizeContactNumber('09181112222'));
    }

    /**
     * Test numbers already in +63 format are not modified incorrectly.
     */
    public function test_preserves_already_formatted_plus63_number(): void
    {
        $this->assertEquals('+639171234567', SmsService::formatToPhilippineInternational('+639171234567'));
        $this->assertEquals('+639181112222', SmsService::normalizeContactNumber('+639181112222'));
    }

    /**
     * Test numbers in 639XXXXXXXXX format (without leading plus) convert properly.
     */
    public function test_converts_639_number_without_plus(): void
    {
        $this->assertEquals('+639171234567', SmsService::formatToPhilippineInternational('639171234567'));
    }

    /**
     * Test 10-digit numbers starting with 9 (without leading 0) convert properly.
     */
    public function test_converts_10_digit_number_starting_with_9(): void
    {
        $this->assertEquals('+639171234567', SmsService::formatToPhilippineInternational('9171234567'));
    }

    /**
     * Test numbers with formatting (spaces, hyphens, parentheses) convert properly.
     */
    public function test_converts_formatted_numbers_with_spaces_and_hyphens(): void
    {
        $this->assertEquals('+639171234567', SmsService::formatToPhilippineInternational('0917-123-4567'));
        $this->assertEquals('+639171234567', SmsService::formatToPhilippineInternational('+63 917 123 4567'));
        $this->assertEquals('+639171234567', SmsService::formatToPhilippineInternational('+63-917-123-4567'));
        $this->assertEquals('+639171234567', SmsService::formatToPhilippineInternational('(0917) 123 4567'));
        $this->assertEquals('+639171234567', SmsService::formatToPhilippineInternational('+63 (917) 123-4567'));
    }

    /**
     * Test accidental +6309 format (+63 and 09 entered together).
     */
    public function test_converts_accidental_plus6309_format(): void
    {
        $this->assertEquals('+639171234567', SmsService::formatToPhilippineInternational('+6309171234567'));
        $this->assertEquals('+639171234567', SmsService::formatToPhilippineInternational('6309171234567'));
    }

    /**
     * Test invalid numbers return null and fail validation.
     */
    public function test_invalid_numbers_return_null_and_fail_validation(): void
    {
        $this->assertNull(SmsService::formatToPhilippineInternational(null));
        $this->assertNull(SmsService::formatToPhilippineInternational(''));
        $this->assertNull(SmsService::formatToPhilippineInternational('   '));
        $this->assertNull(SmsService::formatToPhilippineInternational('N/A'));
        $this->assertNull(SmsService::formatToPhilippineInternational('No contact'));
        $this->assertNull(SmsService::formatToPhilippineInternational('12345')); // too short
        $this->assertNull(SmsService::formatToPhilippineInternational('091712345')); // 9 digits, incomplete
        $this->assertNull(SmsService::formatToPhilippineInternational('0281234567')); // landline
        $this->assertNull(SmsService::formatToPhilippineInternational('abcdefghijk')); // non-digits

        $this->assertFalse(SmsService::isValidContactNumber('12345'));
        $this->assertFalse(SmsService::isValidContactNumber('No contact'));
        $this->assertTrue(SmsService::isValidContactNumber('09171234567'));
        $this->assertTrue(SmsService::isValidContactNumber('+639171234567'));
    }

    /**
     * Test formatToLocal converts back to 09XXXXXXXXX.
     */
    public function test_format_to_local(): void
    {
        $this->assertEquals('09171234567', SmsService::formatToLocal('+639171234567'));
        $this->assertEquals('09171234567', SmsService::formatToLocal('09171234567'));
        $this->assertEquals('09171234567', SmsService::formatToLocal('9171234567'));
    }

    /**
     * Test send automatically dispatches +63XXXXXXXXXX to the provider even when 09XXXXXXXXX is passed.
     */
    public function test_send_converts_number_before_dispatching_to_provider(): void
    {
        $mockProvider = new class implements SmsProviderInterface {
            public string $dispatchedTo = '';
            public string $dispatchedMessage = '';

            public function send(string $to, string $message, array $options = []): array
            {
                $this->dispatchedTo = $to;
                $this->dispatchedMessage = $message;
                return [
                    'success' => true,
                    'reference_id' => 'TEST-REF-123',
                    'error' => null,
                    'data' => null,
                ];
            }
        };

        $service = new SmsService($mockProvider);
        $result = $service->send('09171234567', 'Test message content');

        $this->assertTrue($result['success']);
        $this->assertEquals('+639171234567', $mockProvider->dispatchedTo);
        $this->assertEquals('Test message content', $mockProvider->dispatchedMessage);
    }

    /**
     * Test TextBeeHttpProvider makes real API call and handles successful response.
     */
    public function test_textbee_provider_sends_to_gateway_and_handles_success(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'https://api.textbee.dev/api/v1/gateway/send-sms' => \Illuminate\Support\Facades\Http::response([
                'data' => [
                    'smsBatchId' => 'batch-xyz-12345',
                    'success' => true,
                    'message' => 'Messages queued successfully',
                    'recipientCount' => 1,
                ],
            ], 200),
        ]);

        $provider = new \App\Services\Sms\TextBeeHttpProvider('test-api-key', 'https://api.textbee.dev/api/v1/gateway/send-sms', 'device-123');
        $result = $provider->send('09171234567', 'Test SMS via TextBee');

        $this->assertTrue($result['success']);
        $this->assertEquals('batch-xyz-12345', $result['reference_id']);
        $this->assertNull($result['error']);

        \Illuminate\Support\Facades\Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
            return $request->url() === 'https://api.textbee.dev/api/v1/gateway/send-sms' &&
                   $request['recipients'] === ['+639171234567'] &&
                   $request['message'] === 'Test SMS via TextBee' &&
                   $request->hasHeader('x-api-key', 'test-api-key');
        });
    }

    /**
     * Test TextBeeHttpProvider extracts real error details when API returns failure.
     */
    public function test_textbee_provider_handles_api_failure_with_exact_message(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'https://api.textbee.dev/api/v1/gateway/send-sms' => \Illuminate\Support\Facades\Http::response([
                'message' => 'No active devices found to send SMS',
                'error' => 'Device offline',
            ], 400),
        ]);

        $provider = new \App\Services\Sms\TextBeeHttpProvider('test-api-key', 'https://api.textbee.dev/api/v1/gateway/send-sms', 'device-123');
        $result = $provider->send('09171234567', 'Test SMS message');

        $this->assertFalse($result['success']);
        $this->assertNull($result['reference_id']);
        $this->assertStringContainsString('No active devices found to send SMS', $result['error']);
    }
}
