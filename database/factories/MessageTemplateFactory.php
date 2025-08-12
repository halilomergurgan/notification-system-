<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MessageTemplate>
 */
class MessageTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->regexify('[A-Z]{2}[0-9]{3}'),
            'name' => fake()->sentence(3),
            'content' => 'Değerli müşterimiz {name}, {date} tarihli randevunuzu onaylıyoruz.',
            'variables' => ['name', 'date'],
            'type' => fake()->randomElement(['sms', 'email']),
            'is_active' => fake()->boolean(80),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    /**
     * @return MessageTemplateFactory|Factory
     */
    public function smsTemplate()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'sms',
                'content' => 'Sayın {name}, {date} tarihli randevunuz onaylanmıştır.',
                'variables' => ['name', 'date']
            ];
        });
    }

    /**
     * @return MessageTemplateFactory|Factory
     */
    public function emailTemplate()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'email',
                'content' => 'Sayın {name},

                {date} tarihli randevunuz başarıyla oluşturulmuştur.

                Saygılarımızla,
                [Şirket Adı]',
                'variables' => ['name', 'date']
            ];
        });
    }

    /**
     * @return MessageTemplateFactory|Factory
     */
    public function appointmentReminder()
    {
        return $this->state(function (array $attributes) {
            return [
                'code' => 'RANDEVU_' . fake()->unique()->numerify('###'),
                'name' => 'Randevu Hatırlatma',
                'content' => 'Değerli müşterimiz {name}, {date} tarihli randevunuzu hatırlatmak isteriz.',
                'variables' => ['name', 'date']
            ];
        });
    }
}
