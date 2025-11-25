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
        // Registry of roles and permissions from all connected apps
        Schema::create('roles_permissions_registry', function (Blueprint $table) {
            $table->id();
            $table->string('app_identifier', 50)->index();
            $table->enum('entity_type', ['role', 'permission'])->index();
            $table->unsignedInteger('entity_id'); // ID in the source app
            $table->string('entity_name');
            $table->string('guard_name', 50)->nullable();
            $table->json('metadata')->nullable(); // Store description, created_at, etc.
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Composite indexes for efficient queries
            $table->index(['app_identifier', 'entity_type', 'entity_id'], 'idx_app_entity');
            $table->index('entity_name', 'idx_entity_name');
        });

        // Registry of role-permission assignments from all apps
        Schema::create('role_permission_assignments_registry', function (Blueprint $table) {
            $table->id();
            $table->string('app_identifier', 50)->index();
            $table->unsignedInteger('role_id');
            $table->string('role_name');
            $table->unsignedInteger('permission_id');
            $table->string('permission_name');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['app_identifier', 'role_id'], 'idx_app_role');
            $table->index(['app_identifier', 'role_id', 'permission_id'], 'idx_assignment');
            $table->unique(['app_identifier', 'role_id', 'permission_id', 'deleted_at'], 'unique_assignment');
        });

        // Webhook sync logs for monitoring and debugging
        Schema::create('webhook_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('app_identifier', 50)->index();
            $table->string('event_type', 100);
            $table->json('payload')->nullable();
            $table->enum('status', ['success', 'failed', 'pending'])->default('pending');
            $table->text('error_message')->nullable();
            $table->unsignedTinyInteger('retry_count')->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['app_identifier', 'status'], 'idx_app_status');
            $table->index('created_at', 'idx_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_sync_logs');
        Schema::dropIfExists('role_permission_assignments_registry');
        Schema::dropIfExists('roles_permissions_registry');
    }
};
