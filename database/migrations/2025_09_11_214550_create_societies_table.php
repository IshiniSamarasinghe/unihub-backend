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
    Schema::create('societies', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Name of the society
        $table->string('logo_url')->nullable(); // Logo URL (nullable in case there’s no logo)
        $table->timestamps(); // For created_at and updated_at
    });
}

public function down()
{
    Schema::dropIfExists('societies');
}

};
