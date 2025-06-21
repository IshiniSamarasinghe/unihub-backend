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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('university');
            $table->string('faculty');
            $table->date('date');
            $table->time('time');
            $table->string('type');
            $table->string('location')->nullable();
            $table->string('audience');
            $table->string('society');
            $table->string('approver');
            $table->string('media_path')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            // 🔒 Optional: Add foreign key constraint if you want
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
