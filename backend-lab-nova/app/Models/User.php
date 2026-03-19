<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'last_name', 'email', 'password', 'phone', 'status', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    // Define the relationship with the UserDetail model
    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    // Define the relationship with the Role model
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // Static method to create a User record
    public static function createUser($validated)
    {
        $user = self::create([
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'],
            'status' => $validated['status'],
            'role_id' => $validated['role_id'],
        ]);

        return $user;
    }

    // Method to update a User record
    public function updateUser($validated)
    {
        $this->update(array_filter($validated, fn($key) => in_array($key, $this->getFillable()), ARRAY_FILTER_USE_KEY));
    }

    // Scope to filter users that are not assigned to a specific role or module
    public function scopeWhereNotAssigned($query, $assignedUserIds)
    {
        return $query->whereNotIn('id', $assignedUserIds);
    }

    // Scope to order users by id in descending order
    public function scopeOrdered($query)
    {
        return $query->orderBy('id', 'desc');
    }

    // Method to check if the user has access to a specific module
    public function hasModule($moduleSlug)
    {
        return $this->role->hasModule($moduleSlug) ?? false;
    }

    // Method to check if the user is a staff member (Super Admin or Admin)
    public function isStaff(): bool
    {
        if (! $this->status || ! $this->role) {
            return false;
        }

        return in_array($this->role->name, [
            'Super Admin',
            'Admin',
        ]);
    }

    // Method to get the list of modules the user has access to
    public function getModules()
    {
        $modules = collect($this->role?->getModules() ?? []);

        if ($this->isStaff() && ! $modules->contains('dashboard')) {
            $modules->prepend('dashboard');
        }

        return $modules->values()->toArray();
    }

    // Method to get the list of modules with additional info the user has access to
    public function getModulesWithInfo()
    {
        $modules = collect($this->role?->getModulesWithInfo() ?? []);
        if ($this->isStaff()) {

            $dashboard = Module::where('slug', 'dashboard')
                ->where('is_active', true)
                ->first();

            if ($dashboard && ! $modules->contains(fn($m) => $m['slug'] === 'dashboard')) {
                $modules->prepend([
                    'slug' => $dashboard->slug,
                    'name' => $dashboard->name,
                    'icon' => $dashboard->icon,
                    'route' => $dashboard->route,
                    'sort_order' => 1,
                    'children' => [],
                ]);
            }
        }

        return $modules->sortBy('sort_order')->values()->toArray();
    }
}
