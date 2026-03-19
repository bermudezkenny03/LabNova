<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['role_id', 'module_id', 'permission_id'])]
class RoleModulePermission extends Model
{
    // Define the relationship with the Role model
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Define the relationship with the Module model
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    // Define the relationship with the Permission model
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
