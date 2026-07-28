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
        $currentUser = Auth::user();
        $users = User::orderBy('name')->get();
        return view('users.index', compact('users', 'currentUser'));
    }

    public function create()
    {
        $currentUser = Auth::user();
        return view('users.create', compact('currentUser'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();
        
        // Check if trying to create admin role
        if ($request->role === 'admin') {
            // Only allow if no admin exists yet OR current user is admin
            $adminExists = User::where('role', 'admin')->exists();
            if ($adminExists && !$currentUser->isAdmin()) {
                return back()->withErrors([
                    'role' => 'Admin sudah ada. Sistem hanya dapat memiliki 1 Admin.'
                ])->withInput();
            }
        }
        
        // Determine allowed roles based on current user
        $allowedRoles = ['fuelman', 'group_leader'];
        if ($currentUser->isAdmin()) {
            $allowedRoles[] = 'supervisor';
            // Only allow admin creation if no admin exists
            $adminExists = User::where('role', 'admin')->exists();
            if (!$adminExists) {
                $allowedRoles[] = 'admin';
            }
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
        
        // SPV cannot edit Admin users
        if ($currentUser->isSpv() && $user->isAdmin()) {
            abort(403, 'Supervisor tidak dapat mengubah data Admin.');
        }
        
        // SPV can only edit themselves, not other SPV
        if ($currentUser->isSpv() && $user->isSpv() && $user->id !== $currentUser->id) {
            abort(403, 'Supervisor hanya dapat mengubah data dirinya sendiri, tidak dapat mengubah Supervisor lain.');
        }
        
        // Check if trying to change to admin role
        if ($request->role === 'admin' && $user->role !== 'admin') {
            // Only allow if no admin exists yet OR current user is admin
            $adminExists = User::where('role', 'admin')->where('id', '!=', $user->id)->exists();
            if ($adminExists) {
                return back()->withErrors([
                    'role' => 'Admin sudah ada. Sistem hanya dapat memiliki 1 Admin.'
                ])->withInput();
            }
        }
        
        // Determine allowed roles based on current user
        $allowedRoles = ['fuelman', 'group_leader'];
        if ($currentUser->isAdmin()) {
            $allowedRoles[] = 'supervisor';
            // Allow admin role if editing current admin OR no other admin exists
            $otherAdminExists = User::where('role', 'admin')->where('id', '!=', $user->id)->exists();
            if ($user->isAdmin() || !$otherAdminExists) {
                $allowedRoles[] = 'admin';
            }
        } elseif ($currentUser->isSpv()) {
            // Supervisor editing themselves can keep supervisor role
            if ($user->id === $currentUser->id) {
                $allowedRoles[] = 'supervisor';
            }
            // Otherwise only fuelman and group_leader
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
        $currentUser = Auth::user();

        if ($user->id === $currentUser->id) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // SPV cannot delete Admin
        if ($currentUser->isSpv() && $user->isAdmin()) {
            return redirect()->route('users.index')
                ->with('error', 'Supervisor tidak dapat menghapus Admin.');
        }

        // SPV cannot delete other SPV
        if ($currentUser->isSpv() && $user->isSpv()) {
            return redirect()->route('users.index')
                ->with('error', 'Supervisor tidak dapat menghapus Supervisor lain.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
