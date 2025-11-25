<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolePermissionRegistry extends Model
{
    use SoftDeletes;

    protected $table = 'roles_permissions_registry';

    protected $fillable = [
        'app_identifier',
        'entity_type',
        'entity_id',
        'entity_name',
        'guard_name',
        'metadata',
        'last_synced_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_synced_at' => 'datetime',
    ];

    /**
     * Get all assignments for this role (if entity_type is 'role')
     */
    public function assignments()
    {
        if ($this->entity_type !== 'role') {
            return collect();
        }

        return $this->hasMany(
            RolePermissionAssignmentRegistry::class,
            'role_id',
            'entity_id'
        )->where('app_identifier', $this->app_identifier);
    }

    /**
     * Scope to filter by app
     */
    public function scopeForApp($query, string $appIdentifier)
    {
        return $query->where('app_identifier', $appIdentifier);
    }

    /**
     * Scope to filter by entity type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('entity_type', $type);
    }

    /**
     * Scope to get roles only
     */
    public function scopeRoles($query)
    {
        return $query->where('entity_type', 'role');
    }

    /**
     * Scope to get permissions only
     */
    public function scopePermissions($query)
    {
        return $query->where('entity_type', 'permission');
    }
}
