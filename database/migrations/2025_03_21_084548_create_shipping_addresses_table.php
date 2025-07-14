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
        Schema::create('shipping_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); 
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('address_line_1'); 
            $table->string('address_line_2')->nullable(); 
            $table->string('city'); 
            $table->string('state'); 
            $table->string('zip_code'); 
            $table->string('country'); 
            $table->string('phone_number')->nullable();
            $table->string('phone_code')->nullable();
            $table->string('phone_country_code')->nullable();
            $table->enum('type', ['shipping', 'billing']);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_addresses');
    }
};
