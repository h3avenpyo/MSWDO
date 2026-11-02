<?php

namespace Tests\Unit;

use Database\Seeders\SecureSeedAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Hash;
use Mockery;
use PHPUnit\Framework\TestCase;

class SecureSeedAccountTest extends TestCase
{
    protected function tearDown(): void
    {
        Hash::clearResolvedInstances();
        Mockery::close();
        parent::tearDown();
    }

    public function test_existing_password_is_preserved(): void
    {
        $account = Mockery::mock(Model::class)->makePartial();
        $account->exists = true;
        $account->setRawAttributes(['password' => 'existing-hash']);
        $account->shouldReceive('save')->once()->andReturn(true);
        SeedAccountLookup::$account = $account;

        SecureSeedAccount::updateOrCreate(SeedAccountLookup::class, [], []);

        $this->assertSame('existing-hash', $account->password);
    }

    public function test_new_accounts_receive_different_password_hashes(): void
    {
        Hash::swap(new BcryptHasher(['rounds' => 4]));
        $hashes = [];
        for ($i = 0; $i < 2; $i++) {
            $account = Mockery::mock(Model::class)->makePartial();
            $account->shouldReceive('save')->once()->andReturn(true);
            SeedAccountLookup::$account = $account;
            SecureSeedAccount::updateOrCreate(SeedAccountLookup::class, [], []);
            $hashes[] = $account->password;
            $this->assertSame('bcrypt', password_get_info($account->password)['algoName']);
        }
        $this->assertNotSame($hashes[0], $hashes[1]);
    }
}

class SeedAccountLookup
{
    public static Model $account;

    public static function firstOrNew(array $attributes): Model
    {
        return self::$account;
    }
}
