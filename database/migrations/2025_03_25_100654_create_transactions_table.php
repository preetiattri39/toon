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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('order_id'); 
            $table->unsignedBigInteger('user_id'); 
            $table->decimal('amount', 10, 2); 
            $table->string('payment_method'); 
            $table->string('transaction_id')->nullable(); 
            $table->enum('status', ['pending', 'completed', 'failed', 'canceled', 'refunded'])->default('pending'); // Transaction status
            $table->timestamp('paid_at')->nullable(); 
            $table->timestamps(); 
            $table->softDeletes(); 
        
            // Foreign Key constraints
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
