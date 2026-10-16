<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nesa_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('session_token', 100)->index();
            $table->string('title', 255)->default('Percakapan Baru');
            $table->timestamps();
        });

        Schema::create('nesa_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('conversation_id')->constrained('nesa_conversations')->cascadeOnDelete();
            $table->string('role', 20);
            $table->text('content');
            $table->jsonb('tool_calls')->nullable();
            $table->string('tool_call_id', 100)->nullable();
            $table->jsonb('recommended_listing_ids')->nullable();
            $table->integer('tokens_used')->nullable();
            $table->integer('latency_ms')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nesa_messages');
        Schema::dropIfExists('nesa_conversations');
    }
};
