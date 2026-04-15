<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reservation;

class ReservationPolicy
{
    /**
     * Determine if the user can create a reservation
     */
    public function create(User $user): bool
    {
        return $user->status && $user->role?->status;
    }

    /**
     * Determine if the user can update a reservation
     */
    public function update(User $user, Reservation $reservation): bool
    {
        // Only owner can update their own reservation
        return $user->id === $reservation->user_id && $user->status;
    }

    /**
     * Determine if the user can delete/cancel a reservation
     */
    public function cancel(User $user, Reservation $reservation): bool
    {
        // Only the creator or admin can cancel
        $isOwner = $user->id === $reservation->user_id;
        $isAdmin = in_array($user->role?->name, ['Super Admin', 'Administrador', 'Encargado de Laboratorio']);
        
        return ($isOwner || $isAdmin) && $user->status;
    }

    /**
     * Determine if the user can approve a reservation
     * Only Lab Manager, Admin, and Super Admin can approve
     */
    public function approve(User $user): bool
    {
        $allowedRoles = ['Super Admin', 'Administrador', 'Encargado de Laboratorio'];
        
        return in_array($user->role?->name, $allowedRoles) && $user->status;
    }

    /**
     * Determine if the user can reject a reservation
     * Only Lab Manager, Admin, and Super Admin can reject
     */
    public function reject(User $user): bool
    {
        $allowedRoles = ['Super Admin', 'Administrador', 'Encargado de Laboratorio'];
        
        return in_array($user->role?->name, $allowedRoles) && $user->status;
    }

    /**
     * Determine if the user can view reservations
     * Students can only see their own, others can see all
     */
    public function viewAny(User $user): bool
    {
        return $user->status;
    }

    /**
     * Determine if the user can view a specific reservation
     */
    public function view(User $user, Reservation $reservation): bool
    {
        $isOwner = $user->id === $reservation->user_id;
        $isStaff = in_array($user->role?->name, ['Super Admin', 'Administrador', 'Encargado de Laboratorio', 'Docente']);
        
        return ($isOwner || $isStaff) && $user->status;
    }
}
