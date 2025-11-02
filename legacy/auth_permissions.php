<?php

declare(strict_types=1);

/**
 * Legacy permission helpers preserved after the admin-only migration.
 *
 * The functions below reflect the original behaviour of
 * src/common/libs/Auth/main.php prior to simplifying the permission model.
 */
function legacy_server_permission_check(array $serverPermissions, bool $login, string $permissionKey): bool
{
    if (!$login) {
        return false;
    }

    return in_array($permissionKey, $serverPermissions, true);
}

function legacy_instance_permission_check(?array $instancePermissions, bool $login, string $permissionKey): bool
{
    if (!$login) {
        return false;
    }

    if (!$instancePermissions) {
        return false;
    }

    return in_array($permissionKey, $instancePermissions, true);
}
