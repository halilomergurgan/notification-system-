<?php

namespace App\Console\Commands;

use App\Jobs\ProcessMessageQueueJob;
use App\Services\MessageService;
use Illuminate\Console\Command;

class DispatchMessageQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:dispatch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch message queue processing job';

    public function __construct(
        protected MessageService $messageService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching message queue job...');

        ProcessMessageQueueJob::dispatch();

        $this->info('Job dispatched successfully.');

        return Command::SUCCESS;
    }
}
