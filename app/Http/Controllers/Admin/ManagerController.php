<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    public function index()
    {
        $managers = User::where('role', 'manager')->latest()->paginate(20);

        return view('admin.managers.index', compact('managers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'manager',
        ]);

        return redirect()->route('profile.edit')->with('success_manager', 'Manager account created successfully.');
    }

    public function update(Request $request, User $manager)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $manager->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $manager->name = $request->name;
        $manager->email = $request->email;

        if ($request->filled('password')) {
            $manager->password = Hash::make($request->password);
        }

        $manager->save();

        return redirect()->route('profile.edit')->with('success_manager', 'Manager account updated successfully.');
    }

    public function destroy(User $manager)
    {
        if ($manager->id === auth()->id()) {
            return redirect()->route('profile.edit')->with('error_manager', 'You cannot delete your own admin account from this screen.');
        }

        $manager->delete();

        return redirect()->route('profile.edit')->with('success_manager', 'Manager account deleted successfully.');
    }
}
