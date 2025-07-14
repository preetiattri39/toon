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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('status')->default(true);
            $table->string('ordering')->nullable();
            $table->string('slug')->nullable(); 
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
