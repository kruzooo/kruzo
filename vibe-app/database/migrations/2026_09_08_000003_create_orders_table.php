<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number')->unique();
            $table->string('email');
            $table->string('first_name', 80);
            $table->string('last_name', 80);
            $table->string('phone', 40);
            $table->string('address');
            $table->string('barangay', 100);
            $table->string('city', 100);
            $table->string('province', 100);
            $table->string('postal_code', 20);
            $table->string('payment_method', 20);
            $table->decimal('subtotal', 10, 2);
            $table->json('cart');
            $table->timestamp('placed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
