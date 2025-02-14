<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\BaseRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Inertia\ResponseFactory;
use Throwable;

/**
 * Class LoginController.
 */
class LoginController extends BaseController
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
        return inertia('Auth/Login');
    }

    /**
     * Attempts to log user in.
     *
     * @param LoginRequest $request
     *
     * @return RedirectResponse
     * @throws AuthenticationException
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
            session()->put('auth.company_id', $request->companyId());
        } catch (AuthenticationException $e) {
            return redirect()->intended('/login')
                ->with('error', $e->getMessage());
        }

        return redirect()->intended('/home');
    }
}
