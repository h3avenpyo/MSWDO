<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Financial\FinancialAssistanceApplication;
use App\Models\Senior\SeniorCitizenRecord;
use App\Models\SocialCaseStudy;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MultiDatabaseConnectionTest extends TestCase
{
    #[Test]
    public function models_use_the_single_database_connection(): void
    {
        $this->assertNull((new User())->getConnectionName());
        $this->assertNull((new SeniorCitizenRecord())->getConnectionName());
        $this->assertNull((new Client())->getConnectionName());
        $this->assertNull((new SocialCaseStudy())->getConnectionName());
        $this->assertNull((new FinancialAssistanceApplication())->getConnectionName());

        $this->assertSame(config('database.default'), 'mysql');
    }
}
