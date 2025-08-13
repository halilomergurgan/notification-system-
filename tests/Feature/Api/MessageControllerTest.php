<?php

namespace Tests\Feature\Api;

use App\Models\Message;
use App\Models\MessageQueue;
use App\Models\Recipient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $apiKey;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiKey = 'test-api-key-123';
        config(['app.api_key' => $this->apiKey]);
    }

    /** @test */
    public function messages_endpoint_without_api_key_returns_unauthorized(): void
    {
        $response = $this->getJson('/api/messages');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized'
            ]);
    }

    /** @test */
    public function messages_endpoint_with_invalid_api_key_returns_unauthorized(): void
    {
        $response = $this->withHeaders([
            'X-API-Key' => 'invalid-key'
        ])->getJson('/api/messages');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized'
            ]);
    }

    public function messages_endpoint_returns_sent_messages_by_default(): void
    {
        $sentMessages = MessageQueue::factory()
            ->count(5)
            ->sent()
            ->create();

        $pendingMessages = MessageQueue::factory()
            ->count(3)
            ->pending()
            ->create();

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->getJson('/api/messages');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'message_id',
                        'recipient_id',
                        'personalized_content',
                        'status',
                        'retry_count',
                        'max_retries',
                        'scheduled_at',
                        'sent_at',
                        'provider_message_id',
                        'provider_response',
                        'created_at',
                        'updated_at',
                        'recipient',
                        'message'
                    ]
                ],
                'links',
                'meta'
            ])
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('data.0.status', 'sent');
    }

    /** @test */
    public function messages_endpoint_with_custom_status_parameter(): void
    {
        MessageQueue::factory()->count(3)->sent()->create();
        MessageQueue::factory()->count(7)->pending()->create();
        MessageQueue::factory()->count(2)->failed()->create();

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->getJson('/api/messages?status=pending');

        $response->assertStatus(200)
            ->assertJsonCount(7, 'data');

        $data = $response->json('data');
        foreach ($data as $message) {
            $this->assertEquals('pending', $message['status']);
        }
    }

    /** @test */
    public function messages_endpoint_includes_relationships(): void
    {
        $message = Message::factory()->create([
            'content' => 'Hello {name}, your code is {code}'
        ]);

        $recipient = Recipient::factory()->create([
            'name' => 'John Doe',
            'phone_number' => '5551234567',
            'country_code' => '+90'
        ]);

        $messageQueue = MessageQueue::factory()->sent()->create([
            'message_id' => $message->id,
            'recipient_id' => $recipient->id,
            'personalized_content' => 'Hello John Doe, your code is 123456'
        ]);

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->getJson('/api/messages');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.phone_number', '+905551234567')
            ->assertJsonPath('data.0.content', 'Hello John Doe, your code is 123456')
            ->assertJsonPath('data.0.status', 'sent');
    }
}
