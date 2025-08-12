<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\Recipient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MessageQueue>
 */
class MessageQueueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'processing', 'sent', 'failed', 'cancelled']);
        $isSent = $status === 'sent';

        return [
            'message_id' => Message::factory(),
            'recipient_id' => Recipient::factory(),
            'personalized_content' => function (array $attributes) {
                $message = Message::find($attributes['message_id']);
                $recipient = Recipient::find($attributes['recipient_id']);

                if ($message && $recipient) {
                    return str_replace(
                        ['{name}', '{code}', '{date}'],
                        [
                            $recipient->name,
                            fake()->numerify('######'),
                            fake()->dateTimeBetween('now', '+7 days')->format('d.m.Y')
                        ],
                        $message->content
                    );
                }
                return null;
            },
            'status' => $status,
            'retry_count' => $status === 'failed' ? fake()->numberBetween(1, 3) : 0,
            'max_retries' => 3,
            'scheduled_at' => fake()->dateTimeBetween('now', '+2 days'),
            'sent_at' => $isSent ? now() : null,
            'provider_message_id' => $isSent ? fake()->uuid() : null,
            'provider_response' => $isSent || $status === 'failed' ? [
                'status' => $isSent ? 'success' : 'error',
                'code' => $isSent ? '00' : fake()->randomElement(['101', '102', '103']),
                'message' => $isSent ? 'Başarıyla gönderildi' : 'Gönderim başarısız',
                'timestamp' => now()->timestamp
            ] : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * @return MessageQueueFactory|Factory
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'sent_at' => null,
                'provider_message_id' => null,
                'provider_response' => null
            ];
        });
    }

    /**
     * @return MessageQueueFactory|Factory
     */
    public function sent()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'sent',
                'sent_at' => now(),
                'provider_message_id' => fake()->uuid(),
                'provider_response' => [
                    'status' => 'success',
                    'code' => '00',
                    'message' => 'Başarıyla gönderildi',
                    'timestamp' => now()->timestamp
                ]
            ];
        });
    }

    /**
     * @return MessageQueueFactory|Factory
     */
    public function failed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'failed',
                'retry_count' => fake()->numberBetween(1, 3),
                'provider_response' => [
                    'status' => 'error',
                    'code' => fake()->randomElement(['101', '102', '103']),
                    'message' => fake()->randomElement([
                        'Geçersiz numara',
                        'Servis sağlayıcı hatası',
                        'Yetersiz bakiye'
                    ]),
                    'timestamp' => now()->timestamp
                ]
            ];
        });
    }
}
