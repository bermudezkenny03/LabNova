<?php

namespace App\Helpers;

use App\Models\User;

class AuthorizationHelper
{
    /**
     * Verificar si un usuario tiene permiso en un módulo
     */
    public static function hasPermission(User $user, string $module, string $permission): bool
    {
        // Super Admin siempre tiene acceso
        if ($user->role->name === 'Super Admin') {
            return true;
        }

        return $user->role
            ->modulePermissions()
            ->whereHas('module', function ($query) use ($module) {
                $query->where('slug', $module);
            })
            ->whereHas('permission', function ($query) use ($permission) {
                $query->where('slug', $permission);
            })
            ->exists();
    }

    /**
     * Verificar si un usuario puede aprobar reservas
     */
    public static function canApproveReservations(User $user): bool
    {
        $allowedRoles = ['Super Admin', 'Administrador', 'Encargado de Laboratorio'];
        return in_array($user->role->name, $allowedRoles);
    }

    /**
     * Verificar si un usuario puede rechazar reservas
     */
    public static function canRejectReservations(User $user): bool
    {
        $allowedRoles = ['Super Admin', 'Administrador', 'Encargado de Laboratorio'];
        return in_array($user->role->name, $allowedRoles);
    }

    /**
     * Verificar si un usuario puede gestionar usuarios
     */
    public static function canManageUsers(User $user): bool
    {
        $allowedRoles = ['Super Admin', 'Administrador'];
        return in_array($user->role->name, $allowedRoles);
    }

    /**
     * Verificar si un usuario puede gestionar equipos
     */
    public static function canManageEquipment(User $user): bool
    {
        $allowedRoles = ['Super Admin', 'Administrador', 'Encargado de Laboratorio'];
        return in_array($user->role->name, $allowedRoles);
    }

    /**
     * Verificar si un usuario puede crear reservas
     */
    public static function canCreateReservations(User $user): bool
    {
        $allowedRoles = ['Super Admin', 'Administrador', 'Encargado de Laboratorio', 'Docente', 'Estudiante'];
        return in_array($user->role->name, $allowedRoles);
    }

    /**
     * Verificar si un usuario puede ver solo sus reservas o todas
     */
    public static function canViewAllReservations(User $user): bool
    {
        $allowedRoles = ['Super Admin', 'Administrador', 'Encargado de Laboratorio'];
        return in_array($user->role->name, $allowedRoles);
    }

    /**
     * Verificar si la reserva pertenece al usuario o si puede editarla
     */
    public static function canEditReservation(User $user, $reservation): bool
    {
        // Super Admin y Admin pueden editar cualquier reserva
        if (in_array($user->role->name, ['Super Admin', 'Administrador'])) {
            return true;
        }

        // Encargado de Laboratorio puede editar reservas pendientes
        if ($user->role->name === 'Encargado de Laboratorio') {
            return in_array($reservation->status, ['pending', 'approved']);
        }

        // Otros solo pueden editar sus propias reservas si están pendientes
        return $reservation->user_id === $user->id && $reservation->status === 'pending';
    }
}
