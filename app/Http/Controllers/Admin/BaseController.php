<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public function __construct()
    {
        abort_unless(
            auth()->check() && auth()->user()->role === 'admin',
            403
        );
    }
}
