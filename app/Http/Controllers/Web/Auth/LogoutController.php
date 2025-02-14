<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\BaseRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Response;
use Inertia\ResponseFactory;
use Throwable;

/**
 * Class LogoutController.
 */
class LogoutController extends BaseController
{
    /**
     * Returns a view with an input for username (phone or email).
     *
     * @param BaseRequest $request
     *
     * @return Response|ResponseFactory
     * @SuppressWarnings(PHPMD)
     */
    public function view(BaseRequest $request): Response|ResponseFactory
    {
        return inertia('Auth/Logout');
    }

    /**
     * Returns auth view.
     *
     * @param LogoutRequest $request
     */
    public function logout(LogoutRequest $request): Application|ApiResponse|Redirector|RedirectResponse
    {
        try {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('success', 'Successfully logged out.');
        } catch (Throwable) {
            return redirect('/logout')->withErrors('Failed to log out.');
        }
    }
}
