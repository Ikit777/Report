# Username & Email Login Documentation

## Overview
Sistem sekarang mendukung login menggunakan **Username** atau **Email**, memberikan fleksibilitas kepada user untuk memilih metode login yang mereka preferensi.

## Features

### 1. Dual Login Method
User dapat login menggunakan:
- **Username** - contoh: `johndoe`, `admin123`
- **Email** - contoh: `john@company.com`

### 2. Username Field
- **Format**: Hanya huruf (a-z, A-Z), angka (0-9), dan underscore (_)
- **Case Insensitive**: Username disimpan dalam lowercase untuk konsistensi
- **Unique**: Setiap username harus unik di sistem
- **Max Length**: 50 karakter

### 3. Auto-Detection
Sistem akan otomatis mendeteksi apakah user memasukkan email atau username:
- Jika input berbentuk email (mengandung @), sistem akan mencari berdasarkan email
- Jika tidak berbentuk email, sistem akan mencari berdasarkan username

## User Management

### List Users
Halaman list users (`/users`) sekarang menampilkan kolom **Username** yang ditampilkan dalam format code/monospace untuk kemudahan membaca.

### Create New User
Field yang harus diisi:
1. Nama Lengkap (required)
2. **Username** (required, unique, alphanumeric + underscore)
3. ID Pegawai (optional)
4. Email (required, unique)
5. Role (required)
6. Password (required, min 6 chars)

### Edit User
Semua field termasuk username dapat diedit, dengan validation yang sama seperti saat create.

## Technical Implementation

### Database
```sql
ALTER TABLE users ADD COLUMN username VARCHAR(50) UNIQUE NULL AFTER name;
```

### Validation Rules
```php
'username' => 'required|string|max:50|unique:users,username|regex:/^[a-zA-Z0-9_]+$/'
```

### Login Logic
```php
// Auto-detect login type
$loginValue = strtolower(trim($request->login));
$loginField = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

$credentials = [
    $loginField => $loginValue,
    'password' => $request->password,
];

Auth::attempt($credentials);
```

## Migration Path

### For Existing Users Without Username

#### Option 1: Set Username via Tinker
```bash
php artisan tinker
```

```php
$user = User::find(1);
$user->username = 'admin';
$user->save();
```

#### Option 2: Bulk Update via Migration
Create a data migration to auto-generate usernames from email:

```php
User::whereNull('username')->get()->each(function($user) {
    $username = explode('@', $user->email)[0];
    $username = preg_replace('/[^a-zA-Z0-9_]/', '_', $username);
    
    // Ensure uniqueness
    $baseUsername = $username;
    $counter = 1;
    while (User::where('username', $username)->exists()) {
        $username = $baseUsername . $counter;
        $counter++;
    }
    
    $user->username = strtolower($username);
    $user->save();
});
```

#### Option 3: Make Username Required
If you want to enforce username for all users, update migration to make it NOT NULL:

```php
$table->string('username', 50)->unique()->after('name');
```

Then run the bulk update script above before running the migration.

## Security Considerations

1. **Case Insensitive Storage**: Usernames are stored in lowercase to prevent case-sensitivity issues
2. **Input Sanitization**: Only alphanumeric and underscore characters allowed
3. **Unique Constraint**: Database-level unique constraint prevents duplicates
4. **Trimming**: Input is trimmed to prevent accidental whitespace
5. **Password Requirements**: Minimum 6 characters (consider increasing in production)

## User Experience

### Login Page
- Single input field labeled "Username atau Email"
- Placeholder text: "username atau email@perusahaan.com"
- Error message: "Username/Email atau password yang Anda masukkan salah"

### User List
- Username displayed in monospace font with light background
- Shows "-" if user has no username (legacy data)
- Column order: No, Nama, Username, ID Pegawai, Email, Role, Aksi

### Create/Edit Forms
- Username field with inline help text
- Validation feedback in real-time
- Error messages for invalid format or duplicate username

## Testing Checklist

- [ ] Login with valid username
- [ ] Login with valid email
- [ ] Login with invalid username/email
- [ ] Create user with valid username
- [ ] Create user with duplicate username (should fail)
- [ ] Create user with invalid characters in username (should fail)
- [ ] Edit user and change username
- [ ] Edit user with duplicate username (should fail)
- [ ] View user list and verify username column
- [ ] Create admin user via artisan command with username

## Examples

### Valid Usernames
✅ `john_doe`
✅ `admin123`
✅ `user_2024`
✅ `JohnDoe` (stored as `johndoe`)

### Invalid Usernames
❌ `john.doe` (contains dot)
❌ `john-doe` (contains hyphen)
❌ `john doe` (contains space)
❌ `john@doe` (contains @)

## Files Modified

- `database/migrations/2026_07_28_052739_add_username_to_users_table.php`
- `app/Models/User.php`
- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/UserController.php`
- `app/Console/Commands/CreateAdminUser.php`
- `resources/views/auth/login.blade.php`
- `resources/views/users/index.blade.php`
- `resources/views/users/create.blade.php`
- `resources/views/users/edit.blade.php`
