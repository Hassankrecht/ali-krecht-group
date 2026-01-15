<?php

namespace App\Services;

use App\Models\User;
// use App\Models\Role; // Role model not yet created - disable for now
use Illuminate\Support\Collection;

class RoleService
{
    /**
     * Get all roles
     */
    public function all(): Collection
    {
        return collect([]); // Placeholder - Role model not created
    }

    // Placeholder: Replace $role type and return type with actual Role model when created
    public function grantPermission($role, string $permission)
    {
        // $permission = Permission::where('name', $permission)->firstOrFail(); // Uncomment when Permission model exists
        // $role->permissions()->attach($permission);
        return $role;
    }

    // Placeholder: Replace $role type and return type with actual Role model when created
    public function revokePermission($role, string $permission)
    {
        // $permission = Permission::where('name', $permission)->firstOrFail(); // Uncomment when Permission model exists
        // $role->permissions()->detach($permission);
        return $role;
    }

    /**
     * Get role by ID
     */
    public function getById(int $id) // : Role
    {
        return null; // Placeholder
    }

    /**
     * Get role by name
     */
    public function getByName(string $name) // : Role
    {
        // return Role::where('name', $name)->with('permissions')->firstOrFail();
        return null; // Placeholder
    }

    /**
     * Create a new role
     */
    public function create(array $data) // : Role
    {
        // $role = Role::create([...]);
        // return $role;
        return null; // Placeholder
    }

    /**
     * Update a role
     */
    public function update($role, array $data) // : Role
    {
        // $role->update([...]);
        // return $role;
        return null; // Placeholder
    }

    /**
     * Delete a role
     */
    public function delete($role): bool
    {
        // return $role->delete();
        return false; // Placeholder
    }

    /**
     * Assign role to user
     */
    public function assignRole(User $user, $role): User
    {
        if (is_string($role)) {
            $role = $this->getByName($role);
        }

        $user->roles()->sync([$role->id], false);
        return $user;
    }

    /**
     * Remove role from user
     */
    public function removeRole(User $user, $role): User
    {
        if (is_string($role)) {
            $role = $this->getByName($role);
        }

        $user->roles()->detach($role->id);
        return $user;
    }

    /**
     * Sync roles for user (replace all)
     */
    public function syncRoles(User $user, array $roles): User
    {
        $roleIds = collect($roles)->map(function ($role) {
            if (is_string($role)) {
                $roleObj = $this->getByName($role);
                return $roleObj ? $roleObj->id : null;
            }
            return $role;
        })->filter()->toArray();

        $user->roles()->sync($roleIds);
        return $user;
    }

    /**
     * Check if user has role
     */
    public function hasRole(User $user, $role): bool
    {
        if (is_string($role)) {
            return $user->roles()->where('name', $role)->exists();
        }

        return $user->roles()->where('role_id', $role)->exists();
    }

    /**
     * Get user roles
     */
    public function getUserRoles(User $user): Collection
    {
        return $user->roles()->with('permissions')->get();
    }

    /**
     * Get users with specific role
     */
    public function getUsersByRole($role): Collection
    {
        if (is_string($role)) {
            $role = $this->getByName($role);
        }

        return $role->users()->get();
    }

    /**
     * Check if user can perform action
     */
    public function can(User $user, string $permission): bool
    {
        return $user->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })
            ->exists();
    }

    /**
     * Get all permissions for user
     */
    public function getUserPermissions(User $user): Collection
    {
        return $user->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id');
    }


    /**
     * Get role hierarchy
     */
    public function hierarchy(): array
    {
        return [
            'admin' => 10,
            'manager' => 7,
            'staff' => 5,
            'customer' => 1,
            'guest' => 0,
        ];
    }

    /**
     * Check role hierarchy
     */
    public function canManage(User $manager, User $user): bool
    {
        $managerLevel = $this->getUserLevel($manager);
        $userLevel = $this->getUserLevel($user);

        return $managerLevel > $userLevel;
    }

    /**
     * Get user hierarchy level
     */
    public function getUserLevel(User $user): int
    {
        $hierarchy = $this->hierarchy();
        $userRole = $user->roles()->first();

        return $userRole ? ($hierarchy[$userRole->name] ?? 0) : 0;
    }
}
