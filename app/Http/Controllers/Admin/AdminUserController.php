<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = Admin::orderBy('id', 'desc')->paginate(12);
        return view('admins.admin-users.index', compact('admins'));
    }

    public function create()
    {
        return view('admins.create-admin');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin,email',
            'password' => 'required|string|min:6',
        ]);

        Admin::create($data);

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin created successfully.');
    }

    public function edit(Admin $admin_user)
    {
        return view('admins.edit-admin', ['admin' => $admin_user]);
    }

    public function update(Request $request, Admin $admin_user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin,email,' . $admin_user->id,
            'password' => 'nullable|string|min:6',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $admin_user->update($data);

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin updated successfully.');
    }

    public function destroy(Admin $admin_user)
    {
        // Prevent deleting the last admin account
        if (Admin::count() <= 1) {
            return back()->withErrors(['error' => 'Cannot delete the last admin account.']);
        }

        $admin_user->delete();

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin deleted successfully.');
    }
}
