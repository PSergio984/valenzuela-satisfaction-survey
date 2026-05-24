<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResponsePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Response');
    }

    public function view(AuthUser $authUser, Response $response): bool
    {
        return $authUser->can('View:Response');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Response');
    }

    public function update(AuthUser $authUser, Response $response): bool
    {
        return $authUser->can('Update:Response');
    }

    public function delete(AuthUser $authUser, Response $response): bool
    {
        return $authUser->can('Delete:Response');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Response');
    }

    public function restore(AuthUser $authUser, Response $response): bool
    {
        return $authUser->can('Restore:Response');
    }

    public function forceDelete(AuthUser $authUser, Response $response): bool
    {
        return $authUser->can('ForceDelete:Response');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Response');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Response');
    }

    public function replicate(AuthUser $authUser, Response $response): bool
    {
        return $authUser->can('Replicate:Response');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Response');
    }

}