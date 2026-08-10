<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Client\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        return view('client.dashboard');
    }
}