# INSTRUKSI TEST - MAIN HOLE DEPAN/BELAKANG

## ✅ PERUBAHAN YANG SUDAH DILAKUKAN:

1. **Migration berhasil dijalankan** - kolom `main_hole_variant` sudah ditambahkan ke tabel `daily_report_items`
2. **Database diubah ke SQLite** - karena PostgreSQL/Docker tidak bisa running
3. **Controller sudah diupdate** - menyimpan variant ke kolom `main_hole_variant` (BUKAN di keterangan)
4. **Template sudah diupdate** - membaca dari kolom `main_hole_variant` (BUKAN dari keterangan)
5. **Cache Laravel sudah di-clear**

## 🎯 CARA TEST (SANGAT PENTING - IKUTI STEP BY STEP):

### STEP 1: Clear Browser Cache
Tekan `Cmd + Shift + R` (Mac) atau `Ctrl + Shift + R` (Windows) untuk hard refresh browser Anda.

### STEP 2: Buat Laporan Baru
1. Klik menu **Laporan Harian** → **Buat Laporan Baru**
2. Pilih site: **Sungai Puting**
3. Pilih tanggal: **Hari ini**

### STEP 3: Tambah Row untuk Tank SPM3
1. Klik tombol **Tambah Row** di Section A (Laporan Harian Main Tank)
2. Pada row yang baru:
   - **Pilih Tangki:** SPM3
   - **Sounding Pagi:** 19.0 (atau angka apa saja)
   - **Jam Pagi:** 08:00
   - **Petugas Pagi:** (sudah auto-fill dengan nama Anda)
   - **Sounding Sore:** 18.5 (atau angka apa saja)
   - **Jam Sore:** 16:00
   - **Petugas Sore:** (sudah auto-fill dengan nama Anda)
   - **Keterangan:** (kosongkan atau isi apa saja)

### STEP 4: Simpan Laporan
1. Klik tombol **SIMPAN** di bawah
2. Anda akan dibawa ke halaman detail laporan
3. Status laporan: **DRAFT**

### STEP 5: Edit Laporan (INI YANG PENTING!)
1. Klik tombol **UBAH LAPORAN** atau **EDIT**
2. **PERHATIKAN SECTION A:**
   - Seharusnya ada **3 BARIS** untuk tangki SPM3
   - **Baris 1:** 
     - Main Hole: **DEPAN**
     - Data: terisi (sesuai yang Anda input tadi)
     - Keterangan: kosong atau sesuai yang Anda input
   - **Baris 2:**
     - Main Hole: **BELAKANG**
     - Data: kosong (hanya nama petugas terisi)
     - Keterangan: kosong
   - **Baris 3:**
     - Main Hole: **(DEPAN + BELAKANG) / 2**
     - Data: kosong (hanya nama petugas terisi)
     - Keterangan: kosong

### STEP 6: Isi Data untuk BELAKANG dan (DEPAN + BELAKANG) / 2
1. **Baris 2 (BELAKANG):**
   - Sounding Pagi: 20.0
   - Jam Pagi: 08:00
   - Sounding Sore: 19.5
   - Jam Sore: 16:00
   
2. **Baris 3 ((DEPAN + BELAKANG) / 2):**
   - **JANGAN ISI MANUAL** - ini akan di-calculate otomatis dari DEPAN dan BELAKANG

3. Klik **UPDATE LAPORAN**

### STEP 7: Cek Hasil Akhir
1. Setelah update, buka lagi dengan tombol **EDIT**
2. **Verifikasi:**
   - ✅ 3 baris untuk SPM3 masih ada
   - ✅ Main Hole: DEPAN, BELAKANG, (DEPAN + BELAKANG) / 2
   - ✅ Data DEPAN dan BELAKANG masih tersimpan
   - ✅ Keterangan TIDAK ada tanda `[DEPAN]` atau `[BELAKANG]`

## 🔴 JIKA MASIH TIDAK MUNCUL BENAR:

### Debug 1: Cek Database Langsung
```bash
cd /Users/cal/Pekerjaan/Report
php artisan tinker --execute="
\$items = DB::table('daily_report_items')
    ->join('tanks', 'daily_report_items.tank_id', '=', 'tanks.id')
    ->where('tanks.code', 'SPM3')
    ->select('daily_report_items.id', 'tanks.code', 'daily_report_items.main_hole_variant', 'daily_report_items.sounding_pagi', 'daily_report_items.keterangan')
    ->get();
foreach(\$items as \$item) {
    echo 'ID: ' . \$item->id . ', Variant: ' . (\$item->main_hole_variant ?? 'NULL') . ', Sounding: ' . (\$item->sounding_pagi ?? 'NULL') . ', Ket: ' . (\$item->keterangan ?? 'NULL') . PHP_EOL;
}
"
```

**Hasil yang diharapkan:**
```
ID: 36, Variant: DEPAN, Sounding: 19.0, Ket: NULL
ID: 37, Variant: BELAKANG, Sounding: 20.0, Ket: NULL
ID: 38, Variant: (DEPAN + BELAKANG) / 2, Sounding: NULL, Ket: NULL
```

### Debug 2: Cek Laravel Log
```bash
cd /Users/cal/Pekerjaan/Report
tail -f storage/logs/laravel.log
```

Cari baris yang berisi "Creating 3 rows for DEPAN+BELAKANG tank" atau "Tank X is DEPAN+BELAKANG type"

### Debug 3: Clear Cache Lagi
```bash
cd /Users/cal/Pekerjaan/Report
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## 📋 SUMMARY PERUBAHAN TEKNIS:

**File yang dimodifikasi:**
1. `/Users/cal/Pekerjaan/Report/.env` - DB_CONNECTION diubah dari `pgsql` ke `sqlite`
2. `/Users/cal/Pekerjaan/Report/app/Http/Controllers/ReportController.php`:
   - Line ~589-591: Simpan ke `main_hole_variant` bukan ke keterangan
   - Line ~607, 621, 634: Simpan ke `main_hole_variant` bukan ke keterangan
   - Line ~719: Tambahkan `main_hole_variant` ke createItemFromData
3. `/Users/cal/Pekerjaan/Report/resources/views/reports/partials/report-item-rows.blade.php`:
   - Line ~13-17: Baca dari `main_hole_variant` bukan dari keterangan
   - Line ~32: Tambahkan hidden input untuk `main_hole_variant`
   - Line ~58: Keterangan langsung dari database (tidak ada cleaning marker)
4. `/Users/cal/Pekerjaan/Report/database/migrations/2026_07_27_022021_add_main_hole_variant_to_daily_report_items_table.php` - MIGRATION SUDAH DIJALANKAN

**Kolom baru di database:**
- `daily_report_items.main_hole_variant` (nullable string) - menyimpan "DEPAN", "BELAKANG", atau "(DEPAN + BELAKANG) / 2"

## ⚠️ CATATAN PENTING:

1. **Data lama TIDAK akan berubah** karena dibuat sebelum kolom `main_hole_variant` ada
2. **Test dengan laporan BARU** seperti instruksi di atas
3. **Jangan buka laporan lama** untuk test ini - buat yang baru!
4. **Database sekarang menggunakan SQLite** - bukan PostgreSQL lagi (karena Docker tidak bisa running)

---

Jika masih ada masalah, kirimkan screenshot dari:
1. Halaman edit laporan (yang menampilkan 3 baris)
2. Output dari command debug database di atas

Saya akan bantu debug lebih lanjut.
