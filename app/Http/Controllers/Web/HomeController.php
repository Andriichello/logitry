<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Http\Requests\BaseRequest;
use Inertia\Response;
use Inertia\ResponseFactory;

/**
 * Class HomeController.
 */
class HomeController extends BaseController
{
    /**
     * Returns home view.
     *
     * @param BaseRequest $request
     *
     * @return Response|ResponseFactory
     * @SuppressWarnings(PHPMD)
     */
    public function view(BaseRequest $request): Response|ResponseFactory
    {
        return inertia('Home');
    }
}
