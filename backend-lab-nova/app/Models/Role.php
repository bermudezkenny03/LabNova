<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'description', 'status'])]
class Role extends Model
{
    // Define the relationship with the User model
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    // Define the relationship with the RoleModulePermission model
    public function modulePermissions()
    {
        return $this->hasMany(RoleModulePermission::class);
    }

    // Method to order the roles by ID in descending order
    public function scopeOrdered($query)
    {
        return $query->orderBy('id', 'desc');
    }

    // Method to check if the role has a specific module
    public function hasModule($moduleSlug)
    {
        return $this->modulePermissions()
            ->whereHas('module', function ($query) use ($moduleSlug) {
                $query->where('slug', $moduleSlug)->where('is_active', true);
            })
            ->exists();
    }

    // Method to obtain the active modules associated with the role
    public function getModules()
    {
        return $this->modulePermissions()
            ->with('module')
            ->whereHas('module', function ($query) {
                $query->where('is_active', true);
            })
            ->get()
            ->pluck('module.slug')
            ->unique()
            ->values()
            ->toArray();
    }

    // Method to get active modules with additional information associated with the role
    public function getModulesWithInfo()
    {
        $userModulePermissions = $this->getUserModulePermissions();
        $parentModules = $this->getActiveParentModules();

        return $parentModules
            ->map(fn($parent) => $this->buildParentModuleData($parent, $userModulePermissions))
            ->filter() // Remover nulls
            ->sortBy('sort_order')
            ->values()
            ->toArray();
    }

    // Private methods to obtain user module permissions, active parent modules, and build parent module data    private function getUserModulePermissions()
    private function getUserModulePermissions()
    {
        return $this->modulePermissions()
            ->with(['module', 'permission'])
            ->whereHas(
                'module',
                fn($query) =>
                $query->where('is_active', true)->whereNotNull('parent_id')
            )
            ->get()
            ->groupBy('module.slug');
    }

    // Private method to get active parent modules with their children
    private function getActiveParentModules()
    {
        return Module::where('is_active', true)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();
    }

    // Private method to build parent module data with its visible children based on user permissions
    private function buildParentModuleData($parentModule, $userModulePermissions)
    {
        $visibleChildren = $this->getVisibleChildren($parentModule, $userModulePermissions);

        if ($visibleChildren->isEmpty()) {
            return null; // No mostrar padre si no tiene hijos visibles
        }

        return [
            'slug' => $parentModule->slug,
            'name' => $parentModule->name,
            'icon' => $parentModule->icon,
            'route' => $parentModule->route,
            'permissions' => [],
            'sort_order' => $parentModule->sort_order,
            'children' => $visibleChildren->sortBy('sort_order')->values()->toArray()
        ];
    }

    // Private method to get visible children of a parent module based on user permissions
    private function getVisibleChildren($parentModule, $userModulePermissions)
    {
        return $parentModule->children
            ->filter(
                fn($child) =>
                // $child->show_in_sidebar &&
                $userModulePermissions->has($child->slug)
            )
            ->map(fn($child) => [
                'slug' => $child->slug,
                'name' => $child->name,
                'icon' => $child->icon,
                'route' => $child->route,
                'permissions' => $userModulePermissions[$child->slug]->pluck('permission.slug')->toArray(),
                'sort_order' => $child->sort_order,
                'show_in_sidebar' => $child->show_in_sidebar,
            ]);
    }
}
