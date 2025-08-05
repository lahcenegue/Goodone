<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Support\Facades\Cache;

class AdPermissionService
{
    /**
     * Cache key for admin permissions
     */
    const PERMISSION_CACHE_KEY = 'admin_ad_permissions_';
    const CACHE_TTL = 1800; // 30 minutes

    /**
     * Available ad permissions
     */
    const PERMISSIONS = [
        'view_ads' => 'View Advertisements',
        'create_ads' => 'Create Advertisements',
        'edit_ads' => 'Edit Advertisements',
        'delete_ads' => 'Delete Advertisements',
        'approve_ads' => 'Approve/Reject Advertisements',
        'manage_targeting' => 'Manage Ad Targeting',
        'bulk_operations' => 'Perform Bulk Operations',
        'view_analytics' => 'View Ad Analytics',
        'manage_global_settings' => 'Manage Global Ad Settings',
        'manage_budgets' => 'Manage Ad Budgets',
        'export_data' => 'Export Ad Data'
    ];

    /**
     * Permission groups for easier management
     */
    const PERMISSION_GROUPS = [
        'basic' => ['view_ads', 'view_analytics'],
        'editor' => ['view_ads', 'create_ads', 'edit_ads', 'view_analytics'],
        'manager' => ['view_ads', 'create_ads', 'edit_ads', 'manage_targeting', 'bulk_operations', 'view_analytics'],
        'admin' => ['*'] // All permissions
    ];

    /**
     * Check if admin has specific permission
     */
    public function hasPermission(?int $adminId, string $permission): bool
    {
        if (!$adminId) {
            return false;
        }

        // Super admin check (assuming role or is_super_admin field)
        $admin = $this->getAdmin($adminId);
        if (!$admin) {
            return false;
        }

        // If admin is super admin, grant all permissions
        if (isset($admin->role) && $admin->role === 'super_admin') {
            return true;
        }

        if (isset($admin->is_super_admin) && $admin->is_super_admin) {
            return true;
        }

        // Get cached permissions
        $permissions = $this->getAdminPermissions($adminId);

        return in_array($permission, $permissions) || in_array('*', $permissions);
    }

    /**
     * Check multiple permissions
     */
    public function hasAnyPermission(?int $adminId, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($adminId, $permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if admin has all permissions
     */
    public function hasAllPermissions(?int $adminId, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($adminId, $permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get admin permissions (cached)
     */
    public function getAdminPermissions(int $adminId): array
    {
        return Cache::remember(
            self::PERMISSION_CACHE_KEY . $adminId,
            self::CACHE_TTL,
            function () use ($adminId) {
                $admin = $this->getAdmin($adminId);
                if (!$admin) {
                    return [];
                }

                // Default permissions based on role or manual assignment
                if (isset($admin->ad_permissions) && is_array($admin->ad_permissions)) {
                    return $admin->ad_permissions;
                }

                // Fallback to role-based permissions
                $role = $admin->role ?? 'basic';
                return self::PERMISSION_GROUPS[$role] ?? self::PERMISSION_GROUPS['basic'];
            }
        );
    }

    /**
     * Grant permissions to admin
     */
    public function grantPermissions(int $adminId, array $permissions): bool
    {
        try {
            $admin = $this->getAdmin($adminId);
            if (!$admin) {
                return false;
            }

            // Validate permissions
            $validPermissions = array_keys(self::PERMISSIONS);
            $permissions = array_intersect($permissions, $validPermissions);

            // Update admin permissions (assuming ad_permissions field exists)
            $admin->update(['ad_permissions' => $permissions]);

            // Clear cache
            $this->clearPermissionCache($adminId);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Revoke permissions from admin
     */
    public function revokePermissions(int $adminId, array $permissions): bool
    {
        try {
            $admin = $this->getAdmin($adminId);
            if (!$admin) {
                return false;
            }

            $currentPermissions = $this->getAdminPermissions($adminId);
            $newPermissions = array_diff($currentPermissions, $permissions);

            $admin->update(['ad_permissions' => $newPermissions]);

            // Clear cache
            $this->clearPermissionCache($adminId);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get permission requirements for specific operations
     */
    public function getOperationPermissions(): array
    {
        return [
            'view_ads_list' => ['view_ads'],
            'create_ad' => ['create_ads'],
            'edit_ad' => ['edit_ads'],
            'delete_ad' => ['delete_ads'],
            'approve_ad' => ['approve_ads'],
            'bulk_activate' => ['bulk_operations'],
            'bulk_delete' => ['bulk_operations', 'delete_ads'],
            'manage_targeting' => ['manage_targeting'],
            'view_analytics' => ['view_analytics'],
            'global_settings' => ['manage_global_settings'],
            'budget_management' => ['manage_budgets'],
            'export_reports' => ['export_data']
        ];
    }

    /**
     * Check operation permission
     */
    public function canPerformOperation(?int $adminId, string $operation): bool
    {
        $requiredPermissions = $this->getOperationPermissions()[$operation] ?? [];

        if (empty($requiredPermissions)) {
            return false;
        }

        return $this->hasAllPermissions($adminId, $requiredPermissions);
    }

    /**
     * Get admin model
     */
    private function getAdmin(int $adminId): ?Admin
    {
        try {
            return Admin::find($adminId);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Clear permission cache for admin
     */
    public function clearPermissionCache(int $adminId): void
    {
        Cache::forget(self::PERMISSION_CACHE_KEY . $adminId);
    }

    /**
     * Get all available permissions
     */
    public function getAllPermissions(): array
    {
        return self::PERMISSIONS;
    }

    /**
     * Get permission groups
     */
    public function getPermissionGroups(): array
    {
        return self::PERMISSION_GROUPS;
    }
}
