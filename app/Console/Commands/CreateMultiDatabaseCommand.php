<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateMultiDatabaseCommand extends Command
{
    protected $signature = 'db:create-multi';

    protected $description = 'Create the MSWDO multi-database schemas in MySQL';

    public function handle(): int
    {
        $databases = [
            config('database.connections.mswdo_admin.database'),
            config('database.connections.mswdo_financial.database'),
            config('database.connections.mswdo_senior.database'),
        ];

        foreach (array_unique($databases) as $database) {
            DB::connection('mysql')->statement("CREATE DATABASE IF NOT EXISTS `{$database}`");
            $this->info("Created or confirmed database: {$database}");
        }

        return self::SUCCESS;
    }
}
