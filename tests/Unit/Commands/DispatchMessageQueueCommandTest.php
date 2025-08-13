<?php

namespace Tests\Unit\Commands;

use App\Jobs\ProcessMessageQueueJob;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DispatchMessageQueueCommandTest extends TestCase
{
    /** @test */
    public function command_dispatches_job_successfully()
    {
        Queue::fake();

        $this->artisan('messages:dispatch')
            ->expectsOutput('Dispatching message queue job...')
            ->expectsOutput('Job dispatched successfully.')
            ->assertSuccessful();

        Queue::assertPushed(ProcessMessageQueueJob::class);
    }
}
