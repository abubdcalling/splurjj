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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->longText('heading')->nullable();
            $table->longText('credits')->nullable();
            $table->date('date')->nullable(); // Added date column

            $table->longText('sub_heading')->nullable();
            $table->longText('body')->nullable();
            $table->string('image_1')->nullable();
            $table->string('advertising')->nullable();
            $table->longText('tags')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('subcategory_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
