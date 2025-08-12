<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MessageTemplate::factory(5)->create();

        MessageTemplate::factory(3)->smsTemplate()->create();

        MessageTemplate::factory(2)->emailTemplate()->create();

        MessageTemplate::factory(2)->appointmentReminder()->create();
    }
}
