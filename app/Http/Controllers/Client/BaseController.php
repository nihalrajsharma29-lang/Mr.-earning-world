<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public function __construct()
    {
        abort_unless(
            auth()->check() && auth()->user()->role === 'client',
            403
        );
    }
}
