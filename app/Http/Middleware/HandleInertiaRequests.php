<?php

namespace App\Http\Middleware;

use App\Http\Resources\Specific\MeResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

/**
 * Class HandleInertiaRequests.
 */
class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     * @SuppressWarnings(PHPMD)
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        if ($user instanceof User) {
            $me = new MeResource($user);
        }

        return array_merge(
            parent::share($request),
            [
                'me' => $me ?? null,
                'error' => $request->session()->get('error'),
                'success' => $request->session()->get('success'),
            ]
        );
    }
}
