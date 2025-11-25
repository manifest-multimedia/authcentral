<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolePermissionAssignmentRegistry extends Model
{
    use SoftDeletes;

    protected $table = 'role_permission_assignments_registry';

    protected $fillable = [
        'app_identifier',
        'role_id',
        'role_name',
        'permission_id',
        'permission_name',
        'synced_at',
    ];

    protected $casts = [
        'synced_at' => 'datetime',
    ];

    /**
     * Get the role registry entry
     */
    public function role()
    {
        return $this->belongsTo(
            RolePermissionRegistry::class,
            'role_id',
            'entity_id'
        )->where('app_identifier', $this->app_identifier)
         ->where('entity_type', 'role');
    }

    /**
     * Get the permission registry entry
     */
    public function permission()
    {
        return $this->belongsTo(
            RolePermissionRegistry::class,
            'permission_id',
            'entity_id'
        )->where('app_identifier', $this->app_identifier)
         ->where('entity_type', 'permission');
    }

    /**
     * Scope to filter by app
     */
    public function scopeForApp($query, string $appIdentifier)
    {
        return $query->where('app_identifier', $appIdentifier);
    }

    /**
     * Scope to filter by role
     */
    public function scopeForRole($query, int $roleId)
    {
        return $query->where('role_id', $roleId);
    }
}
