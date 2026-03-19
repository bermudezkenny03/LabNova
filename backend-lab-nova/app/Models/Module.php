<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'slug', 'icon', 'route', 'is_active', 'sort_order', 'show_in_sidebar'])]
class Module extends Model
{
    use SoftDeletes;

    // Define the relationship with the RoleModulePermission model
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_in_sidebar' => 'boolean'
        ];
    }

    // Define the relationship with the RoleModulePermission model
    public function rolePermissions()
    {
        return $this->hasMany(RoleModulePermission::class);
    }

    // Define the relationship with the parent module
    public function parent()
    {
        return $this->belongsTo(Module::class, 'parent_id');
    }

    // Define the relationship with the child modules
    public function children()
    {
        return $this->hasMany(Module::class, 'parent_id')->orderBy('sort_order');
    }

    // Scope to filter active modules
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope to filter modules that should be shown in the sidebar
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    // Scope to filter parent modules (modules without a parent)
    public function scopeChildren($query)
    {
        return $query->whereNotNull('parent_id');
    }
}
