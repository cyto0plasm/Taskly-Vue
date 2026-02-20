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
        Schema::table('tasks', function (Blueprint $table) {
    // For visibility + filters
        $table->index(['creator_id', 'status', 'priority', 'due_date']);

        // For project filtering
        $table->index('project_id');



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
        $table->dropIndex(['creator_id', 'status', 'priority', 'due_date']);
        $table->dropIndex(['project_id']);
        });
    }
};
