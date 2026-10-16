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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('booking_code', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->foreignUuid('room_id')->constrained('rooms')->cascadeOnDelete();

            $table->string('tenant_name');
            $table->string('tenant_phone', 30);
            $table->date('start_date');
            $table->unsignedSmallInteger('duration_months')->default(1);
            $table->date('end_date');

            // Strictly integer Rupiah (Zero Float)
            $table->unsignedBigInteger('total_amount');
            $table->unsignedBigInteger('deposit_amount')->default(0);
            $table->unsignedBigInteger('grand_total');

            $table->string('status', 20)->default('pending')->index();
            $table->string('payment_status', 20)->default('pending')->index();
            $table->text('notes')->nullable();
            $table->timestamp('expired_at')->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['listing_id', 'status']);
            $table->index(['room_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
