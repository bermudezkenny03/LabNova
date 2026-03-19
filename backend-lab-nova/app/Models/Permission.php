<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'slug'])]
class Permission extends Model
{
    use SoftDeletes;

    // Define the relationship with the RoleModulePermission model
    public function roleModules()
    {
        return $this->hasMany(RoleModulePermission::class);
    }
}
