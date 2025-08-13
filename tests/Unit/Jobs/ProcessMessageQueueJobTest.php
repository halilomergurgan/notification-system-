<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessMessageQueueJob;
use App\Services\MessageService;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Mockery;

class ProcessMessageQueueJobTest extends TestCase
{
    /** @test */
    public function job_processes_messages_and_reschedules_with_dispatched_status()
    {
        Queue::fake();

        $messageService = Mockery::mock(MessageService::class);
        $messageService->shouldReceive('processPendingMessages')
            ->once()
            ->with(2)
            ->andReturn([
                ['id' => 1, 'status' => 'dispatched'],
                ['id' => 2, 'status' => 'sent']
            ]);

        $job = new ProcessMessageQueueJob();
        $job->handle($messageService);

        Queue::assertPushed(ProcessMessageQueueJob::class);
    }

    /** @test */
    public function job_processes_messages_and_reschedules_without_dispatched_status()
    {
        Queue::fake();

        $messageService = Mockery::mock(MessageService::class);
        $messageService->shouldReceive('processPendingMessages')
            ->once()
            ->with(2)
            ->andReturn([
                ['id' => 1, 'status' => 'sent'],
                ['id' => 2, 'status' => 'failed']
            ]);

        $job = new ProcessMessageQueueJob();
        $job->handle($messageService);

        Queue::assertPushed(ProcessMessageQueueJob::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
