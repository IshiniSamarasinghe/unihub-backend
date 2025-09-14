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
    Schema::table('societies', function (Blueprint $table) {
        $table->string('logo_url')->nullable(); // Add logo_url column back
    });
}

public function down()
{
    Schema::table('societies', function (Blueprint $table) {
        $table->dropColumn('logo_url'); // Drop the column if we need to rollback
    });
}

};
