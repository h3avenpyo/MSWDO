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
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

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

        if (!$this->app->runningInConsole()) {
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

                $wcpuSchema = \Illuminate\Support\Facades\Schema::connection('wcpu');
                if ($wcpuSchema->hasTable('quarterly_reports') && !$wcpuSchema->hasColumn('quarterly_reports', 'archived_at')) {
                    $wcpuSchema->table('quarterly_reports', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->timestamp('archived_at')->nullable()->index();
                    });
                }
                if ($wcpuSchema->hasTable('report_recipients') && !$wcpuSchema->hasColumn('report_recipients', 'deleted_at')) {
                    $wcpuSchema->table('report_recipients', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->softDeletes();
                    });
                }
                if ($wcpuSchema->hasTable('cases')) {
                    if (!$wcpuSchema->hasColumn('cases', 'priority')) {
                        $wcpuSchema->table('cases', function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->string('priority', 50)->nullable()->default('Non-urgent')->after('status');
                        });
                    }
                    if (!$wcpuSchema->hasColumn('cases', 'abuser_address')) {
                        $wcpuSchema->table('cases', function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->text('abuser_address')->nullable()->after('abuser_name');
                        });
                    }
                    if (!$wcpuSchema->hasColumn('cases', 'bcpc_data')) {
                        $wcpuSchema->table('cases', function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->json('bcpc_data')->nullable();
                        });
                    }
                }
                if ($wcpuSchema->hasTable('vawc_users') && !$wcpuSchema->hasColumn('vawc_users', 'username')) {
                    $wcpuSchema->table('vawc_users', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->string('username', 255)->nullable()->after('email');
                    });
                }
            } catch (\Throwable $e) {
                // Ignore during setup or early boots
            }
        }
    }
}
