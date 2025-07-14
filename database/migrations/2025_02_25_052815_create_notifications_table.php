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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('name')->nullable();    
            $table->string('email')->nullable();
            $table->string('order_number')->nullable();
            $table->string('title')->nullable();    
            $table->string('message')->nullable();
            $table->string('type')->nullable();
            $table->string('notification_type')->nullable();
            $table->boolean('read_status')->default(0);
            $table->timestamp('read_at')->nullable(); 
            $table->integer('ordering')->nullable();
            $table->timestamps(); 
            $table->softDeletes(); 

            // Foreign key constraints (if required)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
