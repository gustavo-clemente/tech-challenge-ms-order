<?php

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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_id');
            $table->uuid('customer_id');
            $table->dateTime('prevision_delivery_date')->nullable();
            $table->enum('status', [
                'CREATED',
                'IN_PREPARATION',
                'DELIVERED',
                'CANCELED',
                'RECEIVED',
                'AWAITING_PAYMENT'
            ])->default('CREATED');
            $table->integer('total_in_cents')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
