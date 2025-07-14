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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('gender')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('phone_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->boolean('status')->default(1);
            $table->string('profile_pic')->nullable();
            $table->boolean('is_email_verified')->nullable();
            $table->string('google_id')->nullable();
            $table->string('google_email')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('facebook_email')->nullable();
            $table->string('apple_id')->nullable();
            $table->string('apple_email')->nullable();
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
