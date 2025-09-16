<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('society_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('society_id');
            $table->unsignedBigInteger('tag_id');

            // Composite PK to prevent duplicates
            $table->primary(['society_id', 'tag_id']);

            // FKs (SQLite supports ON DELETE CASCADE)
            $table->foreign('society_id')
                ->references('id')->on('societies')
                ->onDelete('cascade');

            $table->foreign('tag_id')
                ->references('id')->on('tags')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('society_tag');
    }
};
