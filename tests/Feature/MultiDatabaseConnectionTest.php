<?php

namespace Tests\Feature;

use App\Models\Admin\AdminProfile;
use App\Models\Financial\FinancialAssistanceApplication;
use App\Models\Senior\SeniorCitizenRecord;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MultiDatabaseConnectionTest extends TestCase
{
    #[Test]
    public function models_use_the_expected_database_connections(): void
    {
        $this->assertSame('mswdo_admin', (new User())->getConnectionName());
        $this->assertSame('mswdo_admin', (new AdminProfile())->getConnectionName());
        $this->assertSame('mswdo_financial', (new FinancialAssistanceApplication())->getConnectionName());
        $this->assertSame('mswdo_senior', (new SeniorCitizenRecord())->getConnectionName());
    }
}
