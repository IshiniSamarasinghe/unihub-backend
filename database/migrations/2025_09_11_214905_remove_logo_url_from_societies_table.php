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
        $table->dropColumn('logo_url'); // Drop the logo_url column
    });
}

public function down()
{
    Schema::table('societies', function (Blueprint $table) {
        $table->string('logo_url')->nullable(); // Recreate the column if we need to rollback
    });
}

};
