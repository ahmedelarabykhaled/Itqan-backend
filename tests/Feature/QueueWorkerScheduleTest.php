<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class QueueWorkerScheduleTest extends TestCase
{
    public function test_queue_worker_is_scheduled_every_minute(): void
    {
        $schedule = app(Schedule::class);

        $queueEvent = collect($schedule->events())->first(
            fn ($event) => str_contains($event->command ?? '', 'queue:work --stop-when-empty')
        );

        $this->assertNotNull($queueEvent);
        $this->assertSame('* * * * *', $queueEvent->expression);
    }
}
