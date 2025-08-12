<?php

namespace Database\Factories;

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
        $type = fake()->randomElement(['sms', 'email']);

        $titles = [
            "Sipariş Bildirimi",
            "Randevu Onayı",
            "Kampanya Duyurusu",
            "Kargo Takip Bildirimi",
            "Ödeme Onayı",
            "Güvenlik Uyarısı",
            "Hoşgeldin Mesajı",
            "Doğrulama Kodu",
            "İndirim Fırsatı",
            "Teslimat Bilgisi"
        ];

        $smsTemplates = [
            "Sayın {name}, siparişiniz hazır! Takip kodu: {code}",
            "Değerli müşterimiz, {date} tarihli randevunuzu onaylıyoruz.",
            "İndirim kampanyamız başladı! Kodunuz: {code}",
            "Siparişiniz yola çıktı! Takip no: {code}",
            "Sayın {name}, ödemeniz alındı. İyi alışverişler!",
        ];

        $emailTemplates = [
            "Sayın {name},\n\nSiparişiniz başarıyla oluşturuldu. Sipariş detaylarınız:\nSipariş Kodu: {code}\nTeslimat Tarihi: {date}\n\nBizi tercih ettiğiniz için teşekkür ederiz.",
            "Değerli Müşterimiz {name},\n\nMağazamızdaki özel kampanyalardan faydalanmak için {code} kodunu kullanabilirsiniz. Kampanya {date} tarihine kadar geçerlidir.\n\nSaygılarımızla.",
            "Merhaba {name},\n\nHesabınızda şüpheli bir işlem tespit ettik. Güvenliğiniz için lütfen {code} kodunu kullanarak doğrulama yapınız.\n\nİyi günler dileriz.",
        ];

        $content = $type === 'sms'
            ? fake()->randomElement($smsTemplates)
            : fake()->randomElement($emailTemplates);

        return [
            'title' => fake()->randomElement($titles),
            'content' => $content,
            'type' => $type,
            'character_count' => strlen($content),
            'sms_count' => $type === 'sms' ? ceil(strlen($content) / 160) : 1,
            'variables' => json_encode([
                'name' => '{name}',
                'code' => '{code}',
                'date' => '{date}'
            ]),
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
            $smsTemplates = [
                "Sayın {name}, siparişiniz hazır! Takip kodu: {code}",
                "Değerli müşterimiz, {date} tarihli randevunuzu onaylıyoruz.",
                "İndirim kampanyamız başladı! Kodunuz: {code}",
                "Siparişiniz yola çıktı! Takip no: {code}",
                "Sayın {name}, ödemeniz alındı. İyi alışverişler!",
            ];

            $content = fake()->randomElement($smsTemplates);
            return [
                'type' => 'sms',
                'content' => $content,
                'character_count' => strlen($content),
                'sms_count' => ceil(strlen($content) / 160),
            ];
        });
    }

    /**
     * @return MessageFactory|Factory
     */
    public function email()
    {
        return $this->state(function (array $attributes) {
            $emailTemplates = [
                "Sayın {name},\n\nSiparişiniz başarıyla oluşturuldu. Sipariş detaylarınız:\nSipariş Kodu: {code}\nTeslimat Tarihi: {date}\n\nBizi tercih ettiğiniz için teşekkür ederiz.",
                "Değerli Müşterimiz {name},\n\nMağazamızdaki özel kampanyalardan faydalanmak için {code} kodunu kullanabilirsiniz. Kampanya {date} tarihine kadar geçerlidir.\n\nSaygılarımızla.",
                "Merhaba {name},\n\nHesabınızda şüpheli bir işlem tespit ettik. Güvenliğiniz için lütfen {code} kodunu kullanarak doğrulama yapınız.\n\nİyi günler dileriz.",
            ];

            $content = fake()->randomElement($emailTemplates);
            return [
                'type' => 'email',
                'content' => $content,
                'character_count' => strlen($content),
                'sms_count' => 1,
            ];
        });
    }
}
