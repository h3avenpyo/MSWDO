<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$ctrl = app(\App\Http\Controllers\Admin\AdminReportController::class);
$req = \Illuminate\Http\Request::create('/admin/dashboard/reports/pdf', 'GET', [
    'service' => 'all',
    'period' => 'monthly',
    'month' => 9,
    'year' => 2026,
]);

$pdf = $ctrl->exportPdf($req);
$content = $pdf->getContent();

echo "PDF Size: " . strlen($content) . " bytes\n";
file_put_contents('scratch/sample_output.pdf', $content);
echo "Saved to scratch/sample_output.pdf\n";
