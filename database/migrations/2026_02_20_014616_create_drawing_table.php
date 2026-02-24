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
        Schema::create('drawings', function (Blueprint $table) {
             $table->id();

    // polymorphic relation
    $table->morphs('drawable');

    // canvas data
    $table->longText('data'); // JSON from canvas
    $table->string('version')->nullable();

    $table->timestamps();

    // $table->index(['drawable_type', 'drawable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drawings');
    }
};
