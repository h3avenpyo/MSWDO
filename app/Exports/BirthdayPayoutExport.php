<?php

namespace App\Exports;

use App\Models\Senior\BirthdayPayout;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class BirthdayPayoutExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths
{
    protected $month;
    protected $year;
    protected $barangay;

    public function __construct($month, $year, $barangay = '')
    {
        $this->month = $month;
        $this->year = $year;
        $this->barangay = $barangay;
    }

    public function collection()
    {
        $query = BirthdayPayout::query()
            ->where('birth_month', $this->month)
            ->where('payout_year', $this->year)
            ->with(['senior', 'releasedBy']);

        $payouts = $query->get();

        if ($this->barangay) {
            $payouts = $payouts->filter(function ($payout) {
                return $payout->senior->barangay === $this->barangay;
            });
        }

        return $payouts;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Control No.',
            'OSCA ID',
            'Full Name',
            'Birthday',
            'Age',
            'Barangay',
            'Contact Number',
            'Amount',
            'Status',
            'Released Date',
            'Released By',
            'Remarks'
        ];
    }

    public function map($payout): array
    {
        $senior = $payout->senior;
        $age = $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->age : '-';

        return [
            $payout->id,
            $senior->control_number ?? '-',
            $senior->osca_id ?? '-',
            $senior->full_name,
            $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->format('F d, Y') : '-',
            $age,
            $senior->barangay,
            $senior->contact_number ?? '-',
            number_format($payout->amount, 2),
            ucfirst($payout->status),
            $payout->released_date ? $payout->released_date->format('F d, Y g:i A') : '-',
            $payout->releasedBy ? $payout->releasedBy->name : '-',
            $payout->remarks ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '1A237E']],
                'font' => ['color' => ['rgb' => 'FFFFFF']],
            ],
        ];
    }

    public function title(): string
    {
        return "Birthday Payout - {$this->month} {$this->year}";
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 15,
            'C' => 12,
            'D' => 25,
            'E' => 15,
            'F' => 8,
            'G' => 18,
            'H' => 15,
            'I' => 12,
            'J' => 12,
            'K' => 20,
            'L' => 15,
            'M' => 20,
        ];
    }
}
