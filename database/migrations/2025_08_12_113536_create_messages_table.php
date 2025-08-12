<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('content');
            $table->string('type', 50)->default('sms')->comment('Message type: sms, email');
            $table->integer('character_count')->default(0);
            $table->integer('sms_count')->default(1);
            $table->json('variables')->nullable()->comment('Dynamic variables {name}, {code}');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_active']);
            $table->index(['scheduled_at', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
