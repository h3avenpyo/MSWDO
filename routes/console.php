<?php

use App\Console\Commands\CreateMultiDatabaseCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('db:create-multi', function () {
    $this->call(CreateMultiDatabaseCommand::class);
})->purpose('Create the MSWDO multi-database schemas');
