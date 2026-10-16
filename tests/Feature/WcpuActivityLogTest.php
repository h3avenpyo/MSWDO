<?php

namespace Tests\Feature;

use App\Models\Wcpu\ActivityLog;
use Tests\TestCase;

class WcpuActivityLogTest extends TestCase
{
    public function test_calendar_activity_description_can_be_read_before_and_after_assignment(): void
    {
        $activity = new ActivityLog();

        // The activity logger reads the description before assigning its message.
        $this->assertSame('', $activity->description);

        $activity->description = 'created calendar event: Community meeting';

        $this->assertSame('created calendar event: Community meeting', $activity->description);
        $this->assertSame($activity->description, $activity->toArray()['description']);
    }
}
