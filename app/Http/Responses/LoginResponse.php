<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = $request->user();

        // Determine redirect path based on user role
        $redirectPath = $this->getRedirectPath($user);

        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->intended($redirectPath);
    }

    /**
     * Get the redirect path based on user role.
     */
    protected function getRedirectPath($user): string
    {
        // If user has admin-level roles, redirect to admin panel
        if ($user->hasAnyRole(['super_admin', 'admin', 'staff'])) {
            return '/admin';
        }

        // Default redirect for regular users - go to surveys
        return '/surveys';
    }
}
