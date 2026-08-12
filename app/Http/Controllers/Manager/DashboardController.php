<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Manager\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        return view('manager.dashboard');
    }
}
