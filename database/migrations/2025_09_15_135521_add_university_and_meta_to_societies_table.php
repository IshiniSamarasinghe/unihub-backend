<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('societies', function (Blueprint $table) {
            // existing columns (id, name, logo_url, timestamps) remain
            $table->string('slug')->unique()->nullable()->after('name');

            // FK to universities (nullable so existing rows are safe)
            $table->unsignedBigInteger('university_id')->nullable()->after('slug');
            $table->foreign('university_id')
                ->references('id')->on('universities')
                ->nullOnDelete(); // SET NULL if a university is deleted

            // extra metadata for Join page
            $table->string('join_link')->nullable()->after('logo_url');
            $table->text('description')->nullable()->after('join_link');
            $table->date('registration_open_date')->nullable()->after('description');
            $table->date('registration_close_date')->nullable()->after('registration_open_date');
        });
    }

    public function down(): void
    {
        Schema::table('societies', function (Blueprint $table) {
            // drop in reverse order
            $table->dropForeign(['university_id']);
            $table->dropColumn([
                'registration_close_date',
                'registration_open_date',
                'description',
                'join_link',
                'university_id',
                'slug',
            ]);
        });
    }
};
