<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipients', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number', 20);
            $table->string('country_code', 5)->default('+90');
            $table->string('name', 100)->nullable();
            $table->string('email', 255)->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_blacklisted')->default(false)->index();
            $table->timestamp('last_contact_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'is_blacklisted']);
            $table->unique(['country_code', 'phone_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipients');
    }
};
