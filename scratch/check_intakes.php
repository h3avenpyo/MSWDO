<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SocialCase\BeneficiaryIntake;
use Carbon\Carbon;

echo "Total BeneficiaryIntake: " . BeneficiaryIntake::count() . "\n";
echo "Active (is_archived = false): " . BeneficiaryIntake::where('is_archived', false)->count() . "\n";
echo "Today date_processed: " . BeneficiaryIntake::whereDate('date_processed', Carbon::today())->count() . "\n";
echo "Today created_at: " . BeneficiaryIntake::whereDate('created_at', Carbon::today())->count() . "\n";
echo "is_historical = true: " . BeneficiaryIntake::where('is_historical', true)->count() . "\n";

echo "\nLatest 10 Intakes:\n";
foreach (BeneficiaryIntake::latest('id')->take(10)->get() as $i) {
    echo "ID: {$i->id} | Control: {$i->control_number} | Name: {$i->beneficiary_full_name} | date_processed: {$i->date_processed} | created_at: {$i->created_at} | is_archived: " . ($i->is_archived ? '1' : '0') . " | is_historical: " . ($i->is_historical ? '1' : '0') . "\n";
}
