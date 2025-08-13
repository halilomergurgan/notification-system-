<?php

namespace Tests\Unit\Services;

use App\Models\Message;
use App\Models\MessageQueue;
use App\Models\Recipient;
use App\Repositories\Interfaces\MessageQueueRepositoryInterface;
use App\Services\CacheService;
use App\Services\MessageService;
use App\Services\WebhookService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class MessageServiceTest extends TestCase
{
    protected MessageService $messageService;
    protected MessageQueueRepositoryInterface $mockMessageQueueRepository;
    protected WebhookService $mockWebhookService;
    protected CacheService $mockCacheService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockMessageQueueRepository = Mockery::mock(MessageQueueRepositoryInterface::class);
        $this->mockWebhookService = Mockery::mock(WebhookService::class);
        $this->mockCacheService = Mockery::mock(CacheService::class);

        $this->messageService = new MessageService(
            $this->mockMessageQueueRepository,
            $this->mockWebhookService,
            $this->mockCacheService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_processes_pending_messages_successfully()
    {
        $message = Mockery::mock(Message::class)->makePartial();
        $message->id = 1;
        $message->content = 'Test mesajı';
        $message->type = 'sms';
        $message->character_count = 11;

        $recipient = Mockery::mock(Recipient::class)->makePartial();
        $recipient->id = 1;
        $recipient->country_code = '+90';
        $recipient->phone_number = '5551234567';

        $messageQueue = Mockery::mock(MessageQueue::class)->makePartial();
        $messageQueue->id = 1;
        $messageQueue->message_id = 1;
        $messageQueue->recipient_id = 1;
        $messageQueue->personalized_content = 'Merhaba Test, kodunuz: 123456';
        $messageQueue->message = $message;
        $messageQueue->recipient = $recipient;

        $pendingMessages = new Collection([$messageQueue]);

        $this->mockMessageQueueRepository
            ->shouldReceive('getPendingMessages')
            ->once()
            ->with(2)
            ->andReturn($pendingMessages);

        $this->mockMessageQueueRepository
            ->shouldReceive('updateScheduledAt')
            ->once()
            ->with(1, Mockery::type(\DateTime::class));

        $this->mockWebhookService
            ->shouldReceive('sendMessage')
            ->once()
            ->with('+905551234567', 'Merhaba Test, kodunuz: 123456')
            ->andReturn([
                'messageId' => 'test-message-id',
                'status' => 'sent'
            ]);

        $this->mockMessageQueueRepository
            ->shouldReceive('markAsSent')
            ->once()
            ->with(1, 'test-message-id', [
                'messageId' => 'test-message-id',
                'status' => 'sent'
            ]);

        $this->mockCacheService
            ->shouldReceive('storeMessageInfo')
            ->once()
            ->with('test-message-id', 1, Mockery::type(\DateTime::class));

        $results = $this->messageService->processPendingMessages(2);

        $this->assertCount(1, $results);
        $this->assertEquals('dispatched', $results[0]['status']);
        $this->assertEquals(1, $results[0]['id']);
    }

    /** @test */
    public function it_fails_when_sms_exceeds_character_limit()
    {
        $message = Mockery::mock(Message::class)->makePartial();
        $message->id = 1;
        $message->content = str_repeat('A', 361);
        $message->type = 'sms';
        $message->character_count = 361;

        $recipient = Mockery::mock(Recipient::class)->makePartial();
        $recipient->id = 1;
        $recipient->country_code = '+90';
        $recipient->phone_number = '5551234567';

        $messageQueue = Mockery::mock(MessageQueue::class)->makePartial();
        $messageQueue->id = 1;
        $messageQueue->message_id = 1;
        $messageQueue->recipient_id = 1;
        $messageQueue->personalized_content = null;
        $messageQueue->message = $message;
        $messageQueue->recipient = $recipient;

        $pendingMessages = new Collection([$messageQueue]);

        $this->mockMessageQueueRepository
            ->shouldReceive('getPendingMessages')
            ->once()
            ->with(2)
            ->andReturn($pendingMessages);

        $this->mockMessageQueueRepository
            ->shouldReceive('updateScheduledAt')
            ->once()
            ->with(1, Mockery::type(\DateTime::class));

        $this->mockMessageQueueRepository
            ->shouldReceive('markAsFailed')
            ->once()
            ->with(1, []);

        $this->mockWebhookService
            ->shouldNotReceive('sendMessage');

        $results = $this->messageService->processPendingMessages(2);

        $this->assertCount(1, $results);
        $this->assertEquals('dispatched', $results[0]['status']);
    }

    /** @test */
    public function it_handles_webhook_failure()
    {
        $message = Mockery::mock(Message::class)->makePartial();
        $message->id = 1;
        $message->content = 'Test mesajı';
        $message->type = 'sms';
        $message->character_count = 11;

        $recipient = Mockery::mock(Recipient::class)->makePartial();
        $recipient->id = 1;
        $recipient->country_code = '+90';
        $recipient->phone_number = '5551234567';

        $messageQueue = Mockery::mock(MessageQueue::class)->makePartial();
        $messageQueue->id = 1;
        $messageQueue->message_id = 1;
        $messageQueue->recipient_id = 1;
        $messageQueue->personalized_content = null;
        $messageQueue->message = $message;
        $messageQueue->recipient = $recipient;

        $pendingMessages = new Collection([$messageQueue]);

        $this->mockMessageQueueRepository
            ->shouldReceive('getPendingMessages')
            ->once()
            ->with(2)
            ->andReturn($pendingMessages);

        $this->mockMessageQueueRepository
            ->shouldReceive('updateScheduledAt')
            ->once();

        $this->mockWebhookService
            ->shouldReceive('sendMessage')
            ->once()
            ->andReturn([
                'status' => 'failed',
                'error' => 'Connection timeout'
            ]);

        $this->mockMessageQueueRepository
            ->shouldReceive('markAsFailed')
            ->once()
            ->with(1, [
                'status' => 'failed',
                'error' => 'Connection timeout'
            ]);

        $this->mockCacheService
            ->shouldNotReceive('storeMessageInfo');

        $results = $this->messageService->processPendingMessages(2);

        $this->assertCount(1, $results);
        $this->assertEquals('dispatched', $results[0]['status']);
    }

    /** @test */
    public function it_gets_messages_by_status()
    {
        $paginatedResult = Mockery::mock(LengthAwarePaginator::class);

        $this->mockMessageQueueRepository
            ->shouldReceive('getMessagesByStatus')
            ->once()
            ->with('sent', 20)
            ->andReturn($paginatedResult);

        $result = $this->messageService->getMessagesByStatus('sent', 20);

        $this->assertSame($paginatedResult, $result);
    }
}
