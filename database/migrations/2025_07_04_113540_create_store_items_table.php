<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('store_items', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('faculty');
        $table->string('description');
        $table->string('price');
        $table->string('details');
        $table->string('image_path')->nullable(); // for uploaded image
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_items');
    }
};
