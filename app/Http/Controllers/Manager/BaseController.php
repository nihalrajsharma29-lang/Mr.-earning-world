<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public function __construct()
    {
        abort_unless(
            auth()->check() && auth()->user()->role === 'manager',
            403
        );
    }
}
