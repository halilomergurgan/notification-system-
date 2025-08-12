<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipient>
 */
class RecipientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $turkishNames = [
            'Ahmet Yılmaz', 'Mehmet Demir', 'Ayşe Kaya', 'Fatma Çelik',
            'Mustafa Şahin', 'Emine Yıldız', 'Ali Öztürk', 'Zeynep Aydın',
            'İbrahim Arslan', 'Hatice Güneş', 'Hüseyin Koç', 'Meryem Özdemir',
            'Ömer Kılıç', 'Elif Çetin', 'Yusuf Özkan', 'Hacer Yalçın'
        ];

        $phoneFormats = [
            '53########',
            '54########',
            '55########',
        ];

        $name = fake()->randomElement($turkishNames);
        $emailDomains = ['gmail.com', 'hotmail.com', 'yahoo.com', 'outlook.com'];
        $emailName = strtolower(str_replace(' ', '.', $this->turkishToEnglish($name)));

        return [
            'phone_number' => fake()->numerify(fake()->randomElement($phoneFormats)),
            'country_code' => '+90',
            'name' => $name,
            'email' => $emailName . '@' . fake()->randomElement($emailDomains),
            'is_active' => fake()->boolean(90),
            'is_blacklisted' => fake()->boolean(5),
            'last_contact_at' => fake()->optional(70)->dateTimeBetween('-3 months', 'now'),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    /**
     * @param $string
     * @return string
     */
    private function turkishToEnglish($string): string
    {
        $turkish = ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'];
        $english = ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'];

        return str_replace($turkish, $english, $string);
    }

    /**
     * @return RecipientFactory|Factory
     */
    public function blacklisted()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_blacklisted' => true,
                'is_active' => false
            ];
        });
    }

    /**
     * @return RecipientFactory|Factory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false
            ];
        });
    }
}
