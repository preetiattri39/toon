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
        Schema::create('country_states', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id');
            $table->string('name'); 
            $table->string('state_code')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('ordering')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraint
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_states');
    }
};
