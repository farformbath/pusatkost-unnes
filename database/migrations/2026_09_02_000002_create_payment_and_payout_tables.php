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
        // 1. Extend payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_provider', 50)->default('sandbox')->after('invoice_number');
            $table->string('provider_transaction_id', 100)->nullable()->index()->after('payment_reference');
            $table->timestamp('verified_at')->nullable()->after('paid_at');
            $table->jsonb('provider_payload')->nullable()->after('payload');
            $table->string('idempotency_key', 100)->nullable()->unique()->after('provider_payload');
        });

        // 2. Create payment_reviews table
        Schema::create('payment_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->foreignUuid('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('status', 30)->default('pending')->index();
            $table->boolean('tenant_contacted')->default(false);
            $table->boolean('owner_contacted')->default(false);
            $table->boolean('tenant_confirmed')->default(false);
            $table->boolean('owner_confirmed')->default(false);
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['payment_id', 'status']);
            $table->index(['booking_id', 'status']);
        });

        // 3. Create payouts table
        Schema::create('payouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->foreignUuid('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('gross_amount');
            $table->unsignedBigInteger('platform_fee')->default(0);
            $table->unsignedBigInteger('net_amount');
            $table->string('payout_provider', 50)->default('sandbox');
            $table->string('provider_payout_id', 100)->nullable()->index();
            $table->string('status', 30)->default('pending')->index();
            $table->string('idempotency_key', 100)->unique();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->jsonb('provider_payload')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'status']);
            $table->index(['payment_id', 'status']);
        });

        // 4. Create payment_audits table
        Schema::create('payment_audits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50)->index();
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50)->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();

            $table->index(['payment_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_audits');
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('payment_reviews');

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_provider',
                'provider_transaction_id',
                'verified_at',
                'provider_payload',
                'idempotency_key',
            ]);
        });
    }
};
