<?php

namespace Database\Factories;

use App\Models\MessageTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $template = MessageTemplate::query()
            ->where('is_active', true)
            ->inRandomOrder()
            ->first() ?? MessageTemplate::factory()->create();

        return [
            'title' => $template->name,
            'content' => $template->content,
            'type' => $template->type,
            'character_count' => strlen($template->content),
            'sms_count' => $template->type === 'sms' ? ceil(strlen($template->content) / 160) : 1,
            'variables' => json_encode(array_combine(
                $template->variables,
                array_map(fn($var) => '{'.$var.'}', $template->variables)
            )),
            'template_id' => $template->id,
            'is_active' => fake()->boolean(80),
            'scheduled_at' => fake()->optional(40)->dateTimeBetween('now', '+2 weeks'),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    /**
     * @return MessageFactory|Factory
     */
    public function sms()
    {
        return $this->state(function (array $attributes) {
            $template = MessageTemplate::query()
                ->where('is_active', true)
                ->where('type', 'sms')
                ->inRandomOrder()
                ->first() ?? MessageTemplate::factory()->smsTemplate()->create();

            return [
                'title' => $template->name,
                'content' => $template->content,
                'type' => 'sms',
                'character_count' => strlen($template->content),
                'sms_count' => ceil(strlen($template->content) / 160),
                'template_id' => $template->id,
            ];
        });
    }

    /**
     * @return MessageFactory|Factory
     */
    public function email()
    {
        return $this->state(function (array $attributes) {
            $template = MessageTemplate::query()
                ->where('is_active', true)
                ->where('type', 'email')
                ->inRandomOrder()
                ->first() ?? MessageTemplate::factory()->emailTemplate()->create();

            return [
                'title' => $template->name,
                'content' => $template->content,
                'type' => 'email',
                'character_count' => strlen($template->content),
                'sms_count' => 1,
                'template_id' => $template->id,
            ];
        });
    }
}
