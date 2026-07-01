<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Rate;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class RatePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Rate');
    }

    public function view(AuthUser $authUser, Rate $rate): bool
    {
        return $authUser->can('View:Rate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Rate');
    }

    public function update(AuthUser $authUser, Rate $rate): bool
    {
        return $authUser->can('Update:Rate');
    }

    public function delete(AuthUser $authUser, Rate $rate): bool
    {
        return $authUser->can('Delete:Rate');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Rate');
    }

    public function restore(AuthUser $authUser, Rate $rate): bool
    {
        return $authUser->can('Restore:Rate');
    }

    public function forceDelete(AuthUser $authUser, Rate $rate): bool
    {
        return $authUser->can('ForceDelete:Rate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Rate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Rate');
    }

    public function replicate(AuthUser $authUser, Rate $rate): bool
    {
        return $authUser->can('Replicate:Rate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Rate');
    }
}
