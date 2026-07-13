<?php

namespace App\Http\Controllers\Admin\SocialCase;

use App\Http\Controllers\Controller;
use App\Models\SocialCaseStudy;
use Illuminate\Http\Request;

class SocialCaseModuleController extends Controller
{
    public function released()
    {
        $cases = SocialCaseStudy::on('mswdo_social_case')
            ->with('client')
            ->where(function ($query) {
                $query->where('assistance_released', true)
                    ->orWhere(function ($query) {
                        $query->where('status', 'Closed')
                            ->whereNotNull('assistance_amount');
                    });
            })
            ->latest('assistance_date')
            ->paginate(15);

        return view('admin.social-case.released', compact('cases'));
    }

    public function reports()
    {
        $cases = SocialCaseStudy::on('mswdo_social_case')
            ->with(['client', 'report'])
            ->where('report_generated', true)
            ->latest()
            ->paginate(15);

        return view('admin.social-case.reports', compact('cases'));
    }

    public function exportReports(Request $request)
    {
        $data = $request->validate([
            'case_ids' => ['required', 'array', 'min:1'],
            'case_ids.*' => ['integer'],
        ]);

        $cases = SocialCaseStudy::on('mswdo_social_case')
            ->with('client')
            ->where('report_generated', true)
            ->whereIn('id', $data['case_ids'])
            ->orderBy('case_number')
            ->get();

        return response()->streamDownload(function () use ($cases) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Case Number', 'Client', 'Report Generated', 'Status']);

            foreach ($cases as $case) {
                fputcsv($output, [
                    $case->case_number,
                    $case->client?->full_name,
                    optional($case->report?->generated_at)->format('Y-m-d H:i'),
                    $case->status,
                ]);
            }

            fclose($output);
        }, 'social-case-reports-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }
}
