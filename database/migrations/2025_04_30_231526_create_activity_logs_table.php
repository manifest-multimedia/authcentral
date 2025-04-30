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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('app_name')->nullable(); // Name of the application generating the log
            $table->string('app_url')->nullable(); // URL of the application generating the log
            $table->string('action'); // The action performed (e.g., 'login', 'logout', 'create', 'update', 'delete')
            $table->string('description'); // Human-readable description of the activity
            $table->string('method')->nullable(); // HTTP method used (GET, POST, etc.)
            $table->string('endpoint')->nullable(); // API endpoint accessed
            $table->string('ip_address')->nullable(); // IP address of the user
            $table->string('user_agent')->nullable(); // User's browser/device information
            $table->json('request_data')->nullable(); // Request data in JSON format
            $table->json('entity_data')->nullable(); // Data about the entity that was modified
            $table->string('status')->nullable(); // Status of the action (success, failure, etc.)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
