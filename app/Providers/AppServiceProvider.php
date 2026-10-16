<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (file_exists(__DIR__ . '/../Helpers/helpers.php')) {
            require_once __DIR__ . '/../Helpers/helpers.php';
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if (is_dir(resource_path('views/wcpu'))) {
            \Illuminate\Support\Facades\View::addLocation(resource_path('views/wcpu'));
            \Illuminate\Support\Facades\View::addNamespace('wcpu', resource_path('views/wcpu'));
        }

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('beneficiary_intakes') && !\Illuminate\Support\Facades\Schema::hasColumn('beneficiary_intakes', 'assistance_type')) {
                \Illuminate\Support\Facades\Schema::table('beneficiary_intakes', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('assistance_type', 100)->nullable()->after('assistance_purpose')->index();
                });
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('online_financial_intakes') && !\Illuminate\Support\Facades\Schema::hasColumn('online_financial_intakes', 'assistance_type')) {
                \Illuminate\Support\Facades\Schema::table('online_financial_intakes', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('assistance_type', 100)->nullable()->after('assistance_purpose');
                });
            }
        } catch (\Throwable $e) {
            // Ignore during setup or early boots
        }
    }
}
