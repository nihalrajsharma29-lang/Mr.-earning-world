<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Manager\BaseController;
use App\Models\Customer;

class DashboardController extends BaseController
{
    public function index()
    {
        $pendingHosts = Customer::where('approval_status', 'pending')->count();

        return view('manager.dashboard', compact('pendingHosts'));
    }
}
