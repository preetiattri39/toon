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
        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id'); 
            $table->string('country_code')->nullable();
            $table->string('lang_code')->nullable(); 
            $table->string('name');
            $table->longText('page_content')->nullable();
            $table->timestamps(); 
            $table->softDeletes();

            // Foreign key constraint
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_contents');
    }
};
