<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique()->comment('Template code');
            $table->string('name', 255);
            $table->text('content');
            $table->json('variables')->nullable()->comment('Available variables');
            $table->string('type', 50)->default('sms')->comment('Template type: sms, email');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_active']);
            $table->fullText(['name', 'content']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_templates');
    }
};
