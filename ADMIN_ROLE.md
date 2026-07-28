# Admin Role Documentation

## Overview
Sistem sekarang memiliki role **ADMIN** yang memiliki semua permission seperti SUPERVISOR, dengan tambahan kemampuan untuk mengelola user dengan role SUPERVISOR dan ADMIN.

## Role Hierarchy
1. **Admin** - Akses penuh, bisa mengelola semua user termasuk Supervisor dan Admin lain
2. **Supervisor** - Akses penuh kecuali tidak bisa membuat user Supervisor/Admin
3. **Group Leader** - Akses terbatas untuk verifikasi laporan
4. **Fuelman** - Akses dasar untuk membuat laporan

## Permissions Comparison

### Admin vs Supervisor

| Feature | Admin | Supervisor |
|---------|-------|------------|
| View Dashboard | ✅ | ✅ |
| View Analytics | ✅ | ✅ |
| Approve Reports (SPV) | ✅ | ✅ |
| Reject Reports | ✅ | ✅ |
| Manage Tanks | ✅ | ✅ |
| Tank Calibration | ✅ | ✅ |
| Manage Sites | ✅ | ✅ |
| View Users | ✅ | ✅ |
| Create/Edit Fuelman | ✅ | ✅ |
| Create/Edit Group Leader | ✅ | ✅ |
| **Create/Edit Supervisor** | ✅ | ❌ |
| **Create/Edit Admin** | ✅ | ❌ |

## Creating First Admin User

### Method 1: Using Artisan Command (Recommended)
```bash
php artisan user:create-admin
```

The command will prompt you for:
- Full name
- Email address
- Employee ID (optional)
- Password
- Password confirmation

### Method 2: Using Tinker
```bash
php artisan tinker
```

Then run:
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'employee_id' => 'ADM001',
    'password' => Hash::make('your-password'),
    'role' => 'admin',
]);
```

### Method 3: Update Existing Supervisor to Admin
```bash
php artisan tinker
```

Then run:
```php
$user = User::where('email', 'existing-supervisor@example.com')->first();
$user->role = 'admin';
$user->save();
```

## Usage in Code

### Check if user is Admin
```php
if (auth()->user()->isAdmin()) {
    // Admin-only logic
}
```

### Blade Directives
```blade
@if(auth()->user()->isAdmin())
    <!-- Admin-only content -->
@endif
```

### Route Middleware
```php
Route::get('/admin-only', function() {
    // ...
})->middleware('role:admin');

// Admin OR Supervisor
Route::get('/admin-or-spv', function() {
    // ...
})->middleware('role:admin,spv');
```

## User Management

### As Admin
- Can create users with any role: Fuelman, Group Leader, Supervisor, Admin
- Can edit any user's role
- Can delete any user (except themselves)

### As Supervisor
- Can only create users with role: Fuelman, Group Leader
- Can only edit Fuelman and Group Leader roles
- Cannot create or edit Supervisor or Admin users

## Security Notes

1. **Self-Protection**: Users cannot delete their own account
2. **Role Validation**: Backend validates allowed roles based on current user's role
3. **UI Restrictions**: Role options in form are conditionally shown based on permissions
4. **Route Protection**: All admin-level routes are protected with middleware

## Migration

The `2026_07_28_051913_add_admin_role_to_users_table.php` migration documents the admin role addition.

To upgrade existing supervisors to admin (optional):
```php
DB::table('users')->where('role', 'supervisor')->update(['role' => 'admin']);
```

## Technical Details

### Modified Files
- `app/Models/User.php` - Added `isAdmin()` method
- `app/Http/Controllers/UserController.php` - Added role permission logic
- `app/Http/Middleware/RoleMiddleware.php` - Added admin role mapping
- `routes/web.php` - Updated routes to include admin role
- `resources/views/users/create.blade.php` - Conditional role options
- `resources/views/users/edit.blade.php` - Conditional role options
- `resources/views/users/index.blade.php` - Admin badge display

### Role Constants
Role values stored in database:
- `fuelman`
- `group_leader`
- `supervisor`
- `admin`

### Middleware Aliases
Route middleware supports both full and short role names:
- `admin` → `admin`
- `spv` → `supervisor`
- `gl` → `group_leader`
