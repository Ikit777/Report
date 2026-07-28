<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $currentUser = Auth::user();
        return view('users.create', compact('currentUser'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();
        
        // Determine allowed roles based on current user
        $allowedRoles = ['fuelman', 'group_leader'];
        if ($currentUser->isAdmin()) {
            $allowedRoles[] = 'supervisor';
            $allowedRoles[] = 'admin';
        } elseif ($currentUser->isSpv()) {
            // Supervisor can add fuelman and group_leader only
        }
        
        $request->validate([
            'name'        => 'required|string|max:255',
            'username'    => 'required|string|max:50|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
            'employee_id' => 'nullable|string|max:50|unique:users,employee_id',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:6|confirmed',
            'role'        => 'required|in:' . implode(',', $allowedRoles),
        ]);

        User::create([
            'name'        => $request->name,
            'username'    => strtolower($request->username),
            'employee_id' => $request->employee_id,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();
        return view('users.edit', compact('user', 'currentUser'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();
        
        // Determine allowed roles based on current user
        $allowedRoles = ['fuelman', 'group_leader'];
        if ($currentUser->isAdmin()) {
            $allowedRoles[] = 'supervisor';
            $allowedRoles[] = 'admin';
        } elseif ($currentUser->isSpv()) {
            // Supervisor can edit fuelman and group_leader only
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'username'    => 'required|string|max:50|unique:users,username,' . $user->id . '|regex:/^[a-zA-Z0-9_]+$/',
            'employee_id' => 'nullable|string|max:50|unique:users,employee_id,' . $user->id,
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'role'        => 'required|in:' . implode(',', $allowedRoles),
            'password'    => 'nullable|string|min:6|confirmed',
        ]);

        $data = [
            'name'        => $request->name,
            'username'    => strtolower($request->username),
            'employee_id' => $request->employee_id,
            'email'       => $request->email,
            'role'        => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
