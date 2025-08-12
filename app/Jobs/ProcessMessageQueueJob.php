<?php

namespace App\Jobs;

use App\Services\MessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessMessageQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(MessageService $messageService): void
    {
        Log::info('ProcessMessageQueueJob started');

        $results = $messageService->processPendingMessages(2);

        Log::info('ProcessMessageQueueJob completed', ['results' => $results]);

        $hasDispatched = collect($results)->contains('status', 'dispatched');

        if ($hasDispatched) {
            self::dispatch()->delay(now()->addSeconds(5));

            Log::info('ProcessMessageQueueJob retry 5 second', ['results' => $results]);
        } else {
            self::dispatch()->delay(now()->addSeconds(30));

            Log::info('ProcessMessageQueueJob retry 30 second', ['results' => $results]);
        }
    }
}
