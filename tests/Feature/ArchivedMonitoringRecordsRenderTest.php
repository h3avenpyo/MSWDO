<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ArchivedMonitoringRecordsRenderTest extends TestCase
{
    public static function archiveRoles(): array
    {
        return [
            'desk officer' => ['desk-officer', '2-2026'],
            'BCPC admin' => ['admin', '9-2-2026'],
        ];
    }

    #[DataProvider('archiveRoles')]
    public function test_shared_list_keeps_role_routes_and_restore_guards(string $prefix, string $periodKey): void
    {
        $this->withSession([]);
        $record = (object) [
            'id' => 42, 'barangay_id' => 9, 'quarter' => 2, 'year' => 2026,
            'barangay' => (object) ['name' => "Test O'Neil Barangay"],
            'creator' => (object) ['first_name' => 'Test', 'last_name' => 'Officer'],
            'archived_at' => Carbon::parse('2026-10-20 09:30:00'),
        ];
        $data = [
            'archivedMonitorings' => collect([$record]),
            'archiveRoutePrefix' => $prefix,
            'activeMonitoringPeriods' => [$periodKey => true],
        ];
        $html = view('components.archived-monitoring-records', $data)->render();
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        // Both desktop and card views must expose the authorized view route.
        $links = $xpath->query('//a[@title="View Record"]');
        $this->assertCount(2, $links);
        foreach ($links as $link) {
            $this->assertStringStartsWith(route($prefix.'.bcpc-case-monitoring.edit'), $link->getAttribute('href'));
            parse_str(parse_url($link->getAttribute('href'), PHP_URL_QUERY), $query);
            $this->assertSame('42', $query['view']);
            $this->assertSame('1', $query['archived']);
            if ($prefix === 'admin') {
                $this->assertSame('9', $query['barangay_id']);
                $this->assertSame('monitoring', $link->getAttribute('data-admin-archive-view'));
            }
        }
        $this->assertCount(2, $xpath->query('//button[@disabled]'));

        $data['activeMonitoringPeriods'] = ['10-2-2026' => true];
        $availableHtml = view('components.archived-monitoring-records', $data)->render();
        $this->assertStringNotContainsString(' disabled', $availableHtml);
        if ($prefix === 'admin') {
            $this->assertStringContainsString('restoreMonitoring(42,', $availableHtml);
            $this->assertStringNotContainsString('restore-monitoring-form', $availableHtml);
        } else {
            $this->assertStringContainsString(route('desk-officer.bcpc-case-monitoring.restore', 42), $availableHtml);
        }
    }

    public function test_empty_archive_renders_desktop_and_card_empty_states(): void
    {
        $html = view('components.archived-monitoring-records', ['archivedMonitorings' => collect()])->render();
        $this->assertSame(2, substr_count($html, 'No archived monitoring reports.'));
    }
}
