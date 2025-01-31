<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Interfaces\WithPaginationInterface;
use App\Http\Controllers\Traits\WithPagination;

/**
 * Class BaseController.
 */
abstract class BaseController extends Controller implements
    WithPaginationInterface
{
    use WithPagination;
    use AuthorizesRequests;
    use ValidatesRequests;
}
