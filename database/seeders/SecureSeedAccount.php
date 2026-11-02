<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

final class SecureSeedAccount
{
    /** Preserve existing passwords; new accounts must use password reset to sign in. */
    public static function updateOrCreate(string $model, array $attributes, array $values): Model
    {
        $account = $model::firstOrNew($attributes);

        if (! $account->exists) {
            $account->password = Hash::make(bin2hex(random_bytes(24)));
        }

        $account->fill($values)->save();

        return $account;
    }
}
