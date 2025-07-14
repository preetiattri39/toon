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
        Schema::create('vat_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id')->nullable(); 
            $table->unsignedBigInteger('region_id')->nullable(); 
            $table->string('vat_name')->nullable();
            $table->string('vat_rate');
            $table->longText('description')->nullable();
            $table->boolean('status')->default(1);
            $table->string('ordering')->nullable();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('country_states')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vat_rates');
    }
};
