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
        Schema::create('comics', function (Blueprint $table) {
            $table->id();
            $table->integer('marvel_id', false, true);
            $table->string('title', 255);
            $table->string('details_url', 255)->nullable();
            $table->string('thumbnail', 255)->nullable();
            $table->date('released_on')->nullable();
            $table->timestamps();
            $table->unique('marvel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comics');
    }
};
