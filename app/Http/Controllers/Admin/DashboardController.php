<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        return view('admin.dashboard');
    }

}