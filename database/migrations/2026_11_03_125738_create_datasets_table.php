<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('original_filename');
            $table->string('path');                                   // file location on the private disk
            $table->unsignedBigInteger('size_bytes');

            $table->unsignedBigInteger('row_count')->nullable();      // filled once the file is parsed
            $table->unsignedInteger('column_count')->nullable();
            $table->string('status', 20)->default('pending');         // pending | processing | ready | failed
            $table->unsignedTinyInteger('health_score')->nullable();  // 0-100, drives the Healthy / Needs attention badge

            $table->jsonb('options')->nullable();                     // delimiter, encoding, header row
            $table->jsonb('profile')->nullable();                     // computed analysis results

            $table->timestamps();                                     // created_at is the upload time

            $table->index(['user_id', 'created_at']);                 // "my datasets, newest first"
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datasets');
    }
};