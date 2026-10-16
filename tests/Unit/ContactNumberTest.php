<?php

namespace Tests\Unit;

use App\Rules\ContactNumber;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use PHPUnit\Framework\TestCase;

class ContactNumberTest extends TestCase
{
    public function test_contact_rules_reject_invalid_characters_and_preserve_length_variants(): void
    {
        $factory = new Factory(new Translator(new ArrayLoader(), 'en'));
        foreach (['09171234567', '00000000000'] as $value) {
            $this->assertTrue($factory->make(['contact' => $value], ['contact' => [new ContactNumber(11)]])->passes());
        }
        foreach (['0917abc4567', ' 09171234567', '09171234567 ', '+639171234567', '0917-1234567', '1e123456789', '0917123456', '091712345678', ['09171234567']] as $value) {
            $this->assertTrue($factory->make(['contact' => $value], ['contact' => [new ContactNumber(11)]])->fails());
        }
        $this->assertTrue($factory->make(['phone' => '1234567'], ['phone' => [new ContactNumber(null, 20)]])->passes());
        $this->assertTrue($factory->make(['phone' => str_repeat('1', 21)], ['phone' => [new ContactNumber(null, 20)]])->fails());
        $this->assertTrue($factory->make(['phone' => '1234567'], ['phone' => [new ContactNumber()]])->passes());
        $this->assertTrue($factory->make(['phone' => null], ['phone' => ['nullable', new ContactNumber(11)]])->passes());
    }
}
