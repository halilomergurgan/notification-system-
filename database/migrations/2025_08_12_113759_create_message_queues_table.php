<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipient_id')->constrained()->onDelete('cascade');
            $table->text('personalized_content')->nullable();
            $table->enum('status', ['pending', 'processing', 'sent', 'failed', 'cancelled'])->default('pending');
            $table->integer('retry_count')->default(0);
            $table->integer('max_retries')->default(3);
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('sent_at')->nullable()->index();
            $table->string('provider_message_id')->nullable()->index();
            $table->json('provider_response')->nullable();
            $table->timestamps();

            $table->index(['status', 'scheduled_at']);
            $table->unique(['message_id', 'recipient_id'], 'unique_message_recipient');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_queues');
    }
};
