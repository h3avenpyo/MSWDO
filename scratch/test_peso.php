<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$html = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><style>body{font-family: "DejaVu Sans", sans-serif;}</style></head><body>&#8369; 5,000.00 | ₱ 5,000.00</body></html>';
$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setOption('isFontSubsettingEnabled', true);
$out = $pdf->output();
echo "PDF rendered successfully with size: " . strlen($out) . " bytes\n";
