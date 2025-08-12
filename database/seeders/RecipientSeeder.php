<?php

namespace Database\Seeders;

use App\Models\Recipient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecipientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Recipient::factory(5)->create();

        Recipient::factory(3)->blacklisted()->create();

        Recipient::factory(2)->inactive()->create();
    }
}
