<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateMultiDatabaseCommand extends Command
{
    protected $signature = 'db:create-multi';

    protected $description = 'Create the MSWDO database schema in MySQL';

    public function handle(): int
    {
        $database = config('database.connections.mysql.database');

        DB::connection('mysql')->statement("CREATE DATABASE IF NOT EXISTS `{$database}`");
        $this->info("Created or confirmed database: {$database}");

        return self::SUCCESS;
    }
}
