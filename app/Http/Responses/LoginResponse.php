<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 204);
        }

        // Get locale from route or fallback to app locale
        $locale = $request->route('locale') ?? app()->getLocale();
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('dashboard', ['locale' => $locale]);
        }

        // Redirect to the 'home' route with locale parameter
        return redirect()->route('home', ['locale' => $locale]);
    }
}
