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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('category_id')->nullable(); 
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->enum('product_type', ['simple', 'variation'])->default('simple');
            $table->boolean('status')->default(true);
            $table->string('ordering')->nullable();
            $table->enum('publish', ['published', 'draft'])->default('published');
            $table->string('cover_image')->nullable();
            $table->integer('stock_quantity')->nullable();
            $table->string('regular_price')->nullable();
            $table->string('discounted_price')->nullable();
            $table->string('slug')->nullable();
            $table->string('sku')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
