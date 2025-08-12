<?php

namespace Database\Seeders;

use App\Models\MessageQueue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageQueueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MessageQueue::factory(5)->create();

        MessageQueue::factory(3)->pending()->create();

        MessageQueue::factory(4)->sent()->create();

        MessageQueue::factory(2)->failed()->create();
    }
}
