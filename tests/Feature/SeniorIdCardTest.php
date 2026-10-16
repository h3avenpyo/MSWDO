<?php

namespace Tests\Feature;

use App\Models\Senior\SeniorCitizenRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SeniorIdCardTest extends TestCase
{
    #[Test]
    public function it_generates_id_number_correctly_from_control_number(): void
    {
        $senior = new SeniorCitizenRecord([
            'control_number' => 'SC-HUK-2026-000011',
            'year_applied' => 2026,
        ]);

        $this->assertEquals('SC-2026-000011', $senior->generateSeniorIdNumber());
    }

    #[Test]
    public function it_fallback_generates_id_number_correctly_without_control_number(): void
    {
        $senior = new SeniorCitizenRecord([
            'year_applied' => 2027,
        ]);
        $senior->id = 45;

        $this->assertEquals('SC-2027-000045', $senior->generateSeniorIdNumber());
    }
}
