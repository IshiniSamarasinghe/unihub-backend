<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_items', function (Blueprint $table) {
            $table->date('end_date')->nullable()->after('details'); // ✅ add column
        });
    }

    public function down(): void
    {
        Schema::table('store_items', function (Blueprint $table) {
            $table->dropColumn('end_date');
        });
    }
};
