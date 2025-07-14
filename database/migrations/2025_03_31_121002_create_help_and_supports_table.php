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
        Schema::create('help_and_supports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('name');
            $table->string('email'); 
            $table->string('phone_country_code')->nullable();
            $table->string('phone_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('subject')->nullable(); 
            $table->longText('message')->nullable(); 
            $table->string('ordering')->nullable();
            $table->enum('status', ['open', 'closed', 'pending'])->default('open');
            $table->longText('solutions')->nullable(); 
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');  
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('help_and_supports');
    }
};
