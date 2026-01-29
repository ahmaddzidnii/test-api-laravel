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
        Schema::create('project_images', function (Blueprint $table) {
            $table->id();
            $table->string('path')->unique();
            $table->string('file_name');
            $table->string('file_type');
            $table->integer('file_size');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_used')->default(true);
            $table->timestamps();

            $table->index('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_images');
    }
};
