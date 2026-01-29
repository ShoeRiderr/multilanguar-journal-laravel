<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\EmailVerificationNotificationSentResponse as EmailVerificationNotificationSentResponseContract;
use Laravel\Fortify\Fortify;

class EmailVerificationNotificationSentResponse implements EmailVerificationNotificationSentResponseContract
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
            return new JsonResponse('', 202);
        }

        // Get locale from route or fallback to app locale
        $locale = $request->route('locale') ?? app()->getLocale();

        // Redirect to the 'home' route with locale parameter
        return redirect()->route('home', ['locale' => $locale])
            ->with('status', Fortify::VERIFICATION_LINK_SENT);
    }
}
