# MANUAL APLIKASI DAILY REPORT
### Sistem Pelaporan Harian Kegiatan Fuelman - Warehouse & Inventory

---

## DAFTAR ISI

1. [Pendahuluan](#bab-1-pendahuluan)
2. [Teknologi yang Digunakan](#bab-2-teknologi-yang-digunakan)
3. [Panduan Pengguna - Role Fuelman](#bab-3-panduan-pengguna-role-fuelman)
4. [Panduan Pengguna - Role Group Leader](#bab-4-panduan-pengguna-role-group-leader)
5. [Panduan Pengguna - Role Supervisor](#bab-5-panduan-pengguna-role-supervisor)
6. [Panduan Pengguna - Role Admin](#bab-6-panduan-pengguna-role-admin)
7. [Manfaat dan Saran](#bab-7-manfaat-dan-saran)
8. [Penutup](#penutup)

---

## BAB 1: PENDAHULUAN

### 1.1 Apa itu Aplikasi Daily Report?

**Daily Report** adalah sistem aplikasi web untuk mencatat dan mengelola laporan harian kegiatan Fuelman di area warehouse dan inventory. Aplikasi ini dirancang khusus untuk memudahkan pencatatan sounding tangki BBM, penggunaan flow meter, dan transfer solar secara digital.

### 1.2 Latar Belakang dan Alasan Pembuatan

Sebelum aplikasi ini dibuat, proses pelaporan harian dilakukan secara manual menggunakan kertas atau spreadsheet yang terpisah-pisah. Hal ini menimbulkan beberapa masalah:

**Masalah yang Dihadapi:**
- ❌ Data laporan mudah hilang atau rusak
- ❌ Sulit melacak riwayat perubahan data
- ❌ Proses verifikasi dan approval memakan waktu lama
- ❌ Tidak ada validasi otomatis untuk data yang salah
- ❌ Sulit membuat analisis dan rekap bulanan
- ❌ Foto lampiran sulit diorganisir dan dicari kembali

**Solusi yang Ditawarkan:**
- ✅ Data tersimpan aman di database terpusat
- ✅ Riwayat lengkap dengan tracking status
- ✅ Workflow approval terotomasi (Fuelman → Group Leader → Supervisor)
- ✅ Validasi data otomatis dengan kalibrasi tangki
- ✅ Dashboard dan grafik analisis real-time
- ✅ Manajemen foto terstruktur dengan cloud storage


### 1.3 Tujuan Aplikasi

1. **Digitalisasi Proses Pelaporan** - Menghilangkan ketergantungan pada kertas
2. **Meningkatkan Akurasi Data** - Dengan validasi dan kalibrasi otomatis
3. **Mempercepat Approval** - Workflow digital yang efisien
4. **Memudahkan Analisis** - Dashboard dan laporan rekap otomatis
5. **Meningkatkan Transparansi** - Tracking lengkap setiap perubahan status

### 1.4 Siapa yang Menggunakan Aplikasi Ini?

Aplikasi ini digunakan oleh 4 tipe pengguna:

| Role | Tanggung Jawab |
|------|----------------|
| **Fuelman** | Membuat dan submit laporan harian |
| **Group Leader (GL)** | Memverifikasi laporan dari Fuelman |
| **Supervisor (SPV)** | Menyetujui laporan yang sudah diverifikasi GL |
| **Admin** | Mengelola pengguna, site, dan tangki |

---

## BAB 2: TEKNOLOGI YANG DIGUNAKAN

### 2.1 Teknologi Backend

**Laravel 11.x**
- Framework PHP modern untuk web application
- Menyediakan struktur MVC (Model-View-Controller)
- Built-in security features (CSRF protection, SQL injection prevention)
- Eloquent ORM untuk database management

**PostgreSQL**
- Database relational yang powerful dan reliable
- Support untuk data kompleks dan concurrent users
- ACID compliant untuk data integrity


### 2.2 Teknologi Frontend

**Blade Templates**
- Templating engine Laravel untuk rendering HTML
- Dynamic content generation dengan PHP
- Component reusability

**Vanilla JavaScript**
- Interaktivitas tanpa dependency framework berat
- Form validation real-time
- Dynamic UI updates

**Custom CSS**
- Styling modern dan responsive
- Dark mode support
- Mobile-friendly design

### 2.3 Cloud Storage & File Management

**MinIO / AWS S3**
- Object storage untuk menyimpan foto lampiran
- Scalable dan cost-effective
- Fast retrieval dengan CDN-ready architecture

### 2.4 Deployment & Infrastructure

**Docker**
- Containerization untuk consistent deployment
- Memudahkan scaling horizontal
- Isolasi environment untuk setiap service

**Railway / Cloud Platform**
- Platform hosting untuk production deployment
- Auto-scaling capabilities
- Continuous deployment dari Git repository


### 2.5 Arsitektur Sistem

```
┌─────────────┐
│   Browser   │ ← User Interface (HTML/CSS/JS)
└──────┬──────┘
       │ HTTPS
┌──────▼──────┐
│   Laravel   │ ← Application Logic
│   Server    │   (Routes, Controllers, Models)
└──────┬──────┘
       │
   ┌───┴────┬─────────┐
   │        │         │
┌──▼───┐ ┌─▼──┐ ┌────▼────┐
│ PostgreSQL │ │MinIO│ │ Session │
│ Database   │ │ S3  │ │  Cache  │
└────────────┘ └─────┘ └─────────┘
```

---

## BAB 3: PANDUAN PENGGUNA - ROLE FUELMAN

### 3.1 Gambaran Umum Role Fuelman

**Fuelman** adalah operator lapangan yang bertugas mencatat data sounding tangki BBM dan flow meter setiap hari. Mereka adalah pengguna pertama dalam alur kerja pelaporan.

**Hak Akses Fuelman:**
- ✅ Membuat laporan harian baru
- ✅ Melihat daftar laporan yang pernah dibuat
- ✅ Mengedit laporan dengan status Draft atau Rejected
- ✅ Submit laporan untuk verifikasi Group Leader
- ✅ Melihat detail laporan termasuk feedback dari GL/SPV
- ❌ Tidak bisa approve atau reject laporan
- ❌ Tidak bisa mengakses laporan pengguna lain


### 3.2 Login ke Aplikasi

**Langkah-langkah:**

1. Buka browser dan akses URL aplikasi Daily Report
2. Masukkan **Username atau Email** Anda
3. Masukkan **Password**
4. *(Opsional)* Centang "Ingat Saya" jika menggunakan komputer pribadi
5. Klik tombol **"Masuk"**

**Tips Keamanan:**
- Jangan bagikan password Anda ke orang lain
- Gunakan fitur "Ingat Saya" hanya di perangkat pribadi
- Logout setelah selesai jika menggunakan komputer bersama

### 3.3 Dashboard Fuelman

Setelah login, Anda akan melihat Dashboard dengan informasi:

**Statistik Laporan:**
- **Draft Laporan** - Laporan yang belum di-submit
- **Menunggu Verifikasi (GL)** - Laporan yang sudah submit, menunggu Group Leader
- **Terverifikasi (GL)** - Laporan yang sudah diverifikasi GL, menunggu Supervisor
- **Disetujui (SPV)** - Laporan yang sudah approved Supervisor
- **Perlu Revisi** - Laporan yang ditolak dan harus diperbaiki

**Alert Penting:**
Jika ada laporan yang ditolak, akan muncul notifikasi merah dengan tombol "Revisi Sekarang" untuk mempermudah Anda langsung mengedit.


### 3.4 Membuat Laporan Harian Baru

**Cara Akses:**
- Klik menu **"Laporan Harian"** di sidebar
- Klik tombol **"+ Buat Laporan Baru"** (hijau) di pojok kanan atas

**Langkah-langkah Pengisian:**

#### A. Informasi Dasar
1. **Pilih Site** - Pilih lokasi site kerja Anda (contoh: SPT1 - Sungai Puting)
2. **Tanggal Laporan** - Pilih tanggal laporan (default hari ini)

#### B. Laporan Harian (Main Tank)

Untuk setiap tangki yang akan dilaporkan:

1. **Klik "Tambah Tangki"** untuk menambah baris baru
2. **Pilih Tangki** dari dropdown (contoh: Tangki SPM1, SPM2, dll)
3. **Isi Data Sounding:**
   - **Sounding Pagi** - Ketinggian BBM pagi hari (dalam cm)
   - **Liter Pagi** - Akan terisi otomatis berdasarkan kalibrasi
   - **Sounding Sore** - Ketinggian BBM sore hari (dalam cm)
   - **Liter Sore** - Akan terisi otomatis berdasarkan kalibrasi

4. **Isi Data Flow Meter:**
   - **Jam Pagi** - Waktu pencatatan flow meter pagi
   - **FM Pagi** - Angka flow meter pagi
   - **Jam Sore** - Waktu pencatatan flow meter sore
   - **FM Sore** - Angka flow meter sore
   - **FM Pakai** - Akan dihitung otomatis (FM Sore - FM Pagi)

5. **Upload Foto** - Klik "Pilih Foto" untuk upload foto tangki
   - Tangki dengan Main Hole **TENGAH**: maksimal **8 foto**
   - Tangki dengan Main Hole **DEPAN/BELAKANG**: maksimal **4 foto per row**
   - Format: JPG, JPEG, PNG
   - Ukuran: maksimal 5MB per foto


6. **Keterangan** - *(Opsional)* Tambahkan catatan khusus untuk tangki ini

**Catatan Penting untuk Tangki SPM3 (DEPAN + BELAKANG):**

Jika Anda memilih Tangki SPM3 yang memiliki main hole DEPAN dan BELAKANG, akan otomatis muncul **3 baris**:
- Baris 1: Data untuk Main Hole **DEPAN**
- Baris 2: Data untuk Main Hole **BELAKANG**
- Baris 3: Data untuk **(DEPAN + BELAKANG) / 2** (hasil rata-rata)

Isi data untuk DEPAN dan BELAKANG secara terpisah. Baris rata-rata akan dihitung otomatis oleh sistem.

#### C. Transfer Solar

Jika ada transfer solar ke tangki lain:

1. **Klik "Tambah Transfer"**
2. **Pilih Tangki Tujuan** - Tangki mana yang menerima transfer
3. **Isi Jam Transfer** - Waktu dilakukan transfer
4. **Isi Liter Transfer** - Jumlah liter yang ditransfer
5. **Upload Foto Transfer** - Maksimal **6 foto**
6. **Keterangan** - *(Opsional)* Catatan tambahan

#### D. Simpan atau Submit Laporan

**Opsi 1: Simpan sebagai Draft**
- Klik tombol **"Simpan sebagai Draft"**
- Laporan tersimpan tapi belum masuk ke workflow approval
- Anda masih bisa edit kapan saja

**Opsi 2: Submit untuk Verifikasi**
- Klik tombol **"Submit untuk Verifikasi"**
- Laporan akan masuk ke antrian Group Leader untuk diverifikasi
- Status berubah menjadi "Submitted"
- Anda tidak bisa edit lagi kecuali laporan ditolak


### 3.5 Melihat dan Mengelola Laporan

**Cara Akses:**
- Klik menu **"Laporan Harian"** di sidebar
- Anda akan melihat tabel daftar laporan Anda

**Filter Laporan:**
- **Status** - Filter berdasarkan status (Draft, Submitted, Verified, Approved, Rejected)
- **Site** - Filter berdasarkan site
- **Bulan** - Filter berdasarkan bulan dan tahun

**Aksi yang Tersedia:**

| Status | Aksi yang Bisa Dilakukan |
|--------|--------------------------|
| **Draft** | 👁️ Lihat Detail, ✏️ Edit, 🗑️ Hapus, ✅ Submit |
| **Submitted** | 👁️ Lihat Detail saja |
| **Verified** | 👁️ Lihat Detail saja |
| **Approved** | 👁️ Lihat Detail saja |
| **Rejected** | 👁️ Lihat Detail, ✏️ Edit (untuk revisi), ✅ Submit ulang |

### 3.6 Mengedit Laporan yang Ditolak

Jika laporan Anda ditolak oleh GL atau SPV:

1. **Lihat Feedback** - Di detail laporan, baca catatan penolakan
2. **Klik "Ubah/Revisi"** - Tombol di pojok kanan atas
3. **Perbaiki Data** - Sesuaikan data yang salah berdasarkan feedback
4. **Submit Ulang** - Klik "Submit untuk Verifikasi"

**Tips:**
- Baca feedback dengan teliti sebelum revisi
- Jika ada pertanyaan, tanyakan langsung ke GL/SPV
- Pastikan semua data sudah benar sebelum submit ulang


### 3.7 Tips dan Trik untuk Fuelman

**✅ Best Practices:**

1. **Catat Data Segera** - Input data sounding dan FM sesegera mungkin setelah pengukuran untuk menghindari lupa
2. **Foto yang Jelas** - Pastikan foto sounding dan flow meter jelas dan terbaca
3. **Cek Kalibrasi** - Jika liter menampilkan "XXXX", berarti sounding Anda di luar range kalibrasi. Cek ulang pengukuran
4. **Simpan Draft** - Jika belum selesai, simpan sebagai draft dulu
5. **Submit Tepat Waktu** - Submit laporan sebelum deadline yang ditentukan

**❌ Hal yang Harus Dihindari:**

1. Jangan input data asal-asalan - Data akan diverifikasi oleh GL
2. Jangan gunakan foto yang blur atau tidak relevan
3. Jangan edit laporan yang sudah di-submit (kecuali ditolak)
4. Jangan lupa isi keterangan jika ada kondisi khusus

---

## BAB 4: PANDUAN PENGGUNA - ROLE GROUP LEADER

### 4.1 Gambaran Umum Role Group Leader

**Group Leader (GL)** adalah supervisor tingkat pertama yang bertugas memverifikasi laporan dari Fuelman sebelum diteruskan ke Supervisor.

**Hak Akses Group Leader:**
- ✅ Melihat semua laporan dari semua Fuelman
- ✅ Memverifikasi laporan yang statusnya "Submitted"
- ✅ Menolak laporan dengan memberikan feedback
- ✅ Melihat dashboard dengan statistik verifikasi
- ✅ Akses ke Rekap & Analisis BBM
- ✅ Akses ke Monitoring Tangki
- ✅ Melihat data Tangki BBM
- ❌ Tidak bisa approve final (hanya Supervisor)
- ❌ Tidak bisa mengelola pengguna atau site


### 4.2 Dashboard Group Leader

Dashboard GL menampilkan statistik:

- **Perlu Verifikasi Anda** - Jumlah laporan dengan status "Submitted" yang menunggu verifikasi
- **Telah Anda Verifikasi** - Jumlah laporan yang sudah Anda verifikasi
- **Total Disetujui SPV** - Total laporan yang sudah disetujui Supervisor
- **Telah Anda Tolak** - Jumlah laporan yang pernah Anda tolak

**Tabel Laporan Terbaru:**
Menampilkan 5 laporan terbaru dengan informasi tanggal, site, Fuelman, dan status.

**Daftar Laporan Pending:**
Menampilkan semua laporan yang menunggu verifikasi Anda, diurutkan dari yang tertua (prioritas tinggi).

### 4.3 Memverifikasi Laporan

**Langkah-langkah:**

1. **Buka Dashboard** atau menu **"Laporan Harian"**
2. **Klik laporan** dengan status "Submitted" (badge kuning)
3. **Review Data dengan Teliti:**
   - Cek apakah data sounding masuk akal
   - Cek apakah liter sesuai dengan kalibrasi
   - Cek angka flow meter (FM Pakai = FM Sore - FM Pagi)
   - Lihat foto-foto lampiran
   - Baca keterangan tambahan dari Fuelman

4. **Pilih Aksi:**

   **A. Jika Data Benar - Verifikasi:**
   - Scroll ke bagian "Aksi Group Leader"
   - Klik tombol **"✓ Verifikasi Laporan"** (hijau)
   - Konfirmasi verifikasi
   - Status berubah menjadi "Verified" dan diteruskan ke Supervisor

   **B. Jika Data Salah - Tolak:**
   - Scroll ke bagian "Aksi Group Leader"
   - Klik tombol **"✗ Tolak & Kembalikan"** (merah)
   - **Wajib** isi kotak feedback dengan penjelasan:
     - Apa yang salah?
     - Data mana yang harus diperbaiki?
     - Instruksi yang jelas untuk Fuelman
   - Klik **"Tolak Laporan"**
   - Status berubah menjadi "Rejected" dan kembali ke Fuelman


**Contoh Feedback yang Baik:**

✅ "Sounding sore Tangki SPM1 terlalu tinggi (245 cm), melebihi kapasitas tangki. Mohon cek ulang pengukuran dan update data yang benar."

✅ "Foto flow meter pagi tidak terbaca dengan jelas. Mohon upload ulang foto yang lebih jelas dan fokus pada angka."

❌ "Data salah" (terlalu umum, tidak jelas apa yang salah)

### 4.4 Melihat Rekap & Analisis BBM

**Cara Akses:**
- Klik menu **"Rekap & Analisis"** di sidebar

**Fitur:**

1. **Filter Data:**
   - Pilih **Site**
   - Pilih **Bulan dan Tahun**
   - Klik **"Tampilkan Data"**

2. **Informasi yang Ditampilkan:**
   - **Total Penggunaan BBM** - Total FM Pakai dalam periode
   - **Rata-rata Harian** - Penggunaan per hari
   - **Grafik Penggunaan** - Visualisasi penggunaan per tangki
   - **Tabel Detail** - Penggunaan per tangki dengan min/max/avg
   - **Perbandingan Bulan Lalu** - Trend naik/turun

3. **Export Data:**
   - *(Future feature)* Export ke Excel/PDF

### 4.5 Monitoring Kondisi Tangki

**Cara Akses:**
- Klik menu **"Monitoring Tangki"** di sidebar

**Fitur:**

1. **Filter:**
   - Pilih **Site**
   - Pilih **Tanggal** (default hari ini)

2. **Informasi per Tangki:**
   - **Kapasitas Total** - Kapasitas maksimal tangki
   - **Liter Saat Ini** - Liter sore dari laporan terakhir
   - **Sisa Kapasitas** - Berapa liter lagi bisa diisi
   - **Persentase Pengisian** - Bar chart visual
   - **Status** - Warning jika mendekati penuh atau kosong


### 4.6 Tips untuk Group Leader

**✅ Best Practices:**

1. **Verifikasi Segera** - Jangan tunda verifikasi agar workflow tidak terhambat
2. **Teliti tapi Cepat** - Fokus pada data kritikal (sounding, flow meter)
3. **Feedback Konstruktif** - Berikan instruksi yang jelas saat menolak
4. **Komunikasi** - Jika ada keraguan, hubungi Fuelman langsung
5. **Prioritas** - Verifikasi laporan tertua terlebih dahulu

**❌ Hal yang Harus Dihindari:**

1. Jangan verifikasi tanpa cek detail - Tanggung jawab ada di Anda
2. Jangan tolak tanpa feedback yang jelas
3. Jangan menunda-nunda verifikasi lebih dari 1 hari

---

## BAB 5: PANDUAN PENGGUNA - ROLE SUPERVISOR

### 5.1 Gambaran Umum Role Supervisor

**Supervisor (SPV)** adalah approval terakhir dalam workflow laporan. Supervisor memiliki akses penuh untuk mengelola operasional sistem.

**Hak Akses Supervisor:**
- ✅ Melihat semua laporan dari semua site
- ✅ Meng-approve laporan yang statusnya "Verified"
- ✅ Menolak laporan dengan feedback
- ✅ Dashboard dengan statistik approval
- ✅ Akses ke Rekap & Analisis BBM
- ✅ Akses ke Monitoring Tangki
- ✅ **Kelola Tangki** - Create, Edit, Delete tangki dan kalibrasi
- ✅ **Kelola Site** - Create, Edit, Delete site
- ✅ **Manajemen Pengguna** - Create, Edit, Delete user (kecuali Admin)
- ❌ Tidak bisa edit/delete Admin
- ❌ Tidak bisa edit/delete Supervisor lain (hanya diri sendiri)


### 5.2 Dashboard Supervisor

Dashboard SPV menampilkan:

- **Perlu Persetujuan Anda** - Laporan dengan status "Verified" menunggu approval
- **Telah Anda Setujui** - Total laporan yang sudah Anda approve
- **Total Laporan** - Total semua laporan di sistem
- **Volume Keluar (FM Pakai)** - Total penggunaan BBM dari semua laporan

### 5.3 Meng-Approve Laporan

**Langkah-langkah:**

1. **Buka Dashboard** atau menu **"Laporan Harian"**
2. **Klik laporan** dengan status "Verified" (badge biru)
3. **Review Data:**
   - Data sudah diverifikasi GL, tapi tetap cek ulang jika ada yang mencurigakan
   - Lihat feedback dari GL (jika ada)
   - Pastikan semua data lengkap

4. **Pilih Aksi:**

   **A. Jika Setuju - Approve:**
   - Scroll ke bagian "Aksi Supervisor"
   - Klik tombol **"✓ Setujui Laporan"** (hijau)
   - Konfirmasi approval
   - Status berubah menjadi "Approved" (final)

   **B. Jika Tidak Setuju - Tolak:**
   - Klik tombol **"✗ Tolak & Kembalikan"** (merah)
   - Isi feedback dengan alasan penolakan
   - Klik **"Tolak Laporan"**
   - Status berubah menjadi "Rejected" dan kembali ke Fuelman

**Catatan Penting:**
Setelah status "Approved", laporan tidak bisa diubah lagi oleh siapapun. Pastikan semua data sudah benar sebelum approve.


### 5.4 Mengelola Tangki BBM

**Cara Akses:**
- Klik menu **"Kelola Tangki"** di sidebar

#### A. Menambah Tangki Baru

1. Klik tombol **"+ Tambah Tangki Baru"**
2. Isi form:
   - **Site** - Pilih lokasi site
   - **Kode Tangki** - Nama tangki (contoh: SPM1, SPM2)
   - **Main Hole** - Jenis main hole (TENGAH, DEPAN, BELAKANG, atau custom)
   - **Kapasitas** - Kapasitas maksimal dalam liter
   - **Status Aktif** - Centang jika tangki aktif digunakan
   - **File Kalibrasi** - Upload file Excel (.xlsx) berisi tabel kalibrasi

3. Klik **"Simpan"**

**Format File Kalibrasi:**

File Excel harus memiliki 2 kolom:
- Kolom A: **Sounding (cm)** - Tinggi BBM dalam centimeter
- Kolom B: **Volume (liter)** - Volume BBM dalam liter

Contoh:
```
Sounding | Volume
---------|-------
0        | 0
10       | 500
20       | 1200
30       | 2000
...dst
```

#### B. Mengedit Tangki

1. Klik tombol **"Edit"** (ikon pensil) pada tangki yang ingin diubah
2. Ubah data yang diperlukan
3. Jika ingin update kalibrasi, upload file Excel baru
4. Klik **"Simpan Perubahan"**

**Catatan:** Mengubah kalibrasi akan menghapus data kalibrasi lama dan diganti dengan yang baru.

#### C. Menghapus Tangki

1. Klik tombol **"Hapus"** (ikon trash) pada tangki
2. Konfirmasi penghapusan

**Catatan:** Tangki yang sudah memiliki laporan tidak bisa dihapus untuk menjaga integritas data.


### 5.5 Mengelola Site

**Cara Akses:**
- Klik menu **"Kelola Site"** di sidebar

#### A. Menambah Site Baru

1. Klik tombol **"+ Tambah Site Baru"**
2. Isi form:
   - **Kode Site** - Kode unik site (contoh: SPT1)
   - **Nama Site** - Nama lengkap (contoh: Sungai Puting)
   - **Status Aktif** - Centang jika site aktif
3. Klik **"Simpan"**

#### B. Mengedit Site

1. Klik tombol **"Edit"** pada site
2. Ubah data yang diperlukan
3. Klik **"Simpan Perubahan"**

#### C. Menghapus Site

1. Klik tombol **"Hapus"** pada site
2. Konfirmasi penghapusan

**Catatan:** Site yang sudah memiliki laporan tidak bisa dihapus.

### 5.6 Manajemen Pengguna

**Cara Akses:**
- Klik menu **"Manajemen Pengguna"** di sidebar

**Daftar Pengguna:**
Menampilkan tabel user dengan kolom:
- No
- Nama
- Username
- ID Pegawai
- Email
- Role
- Aksi (Edit/Hapus)

**Catatan:** Supervisor tidak bisa melihat, edit, atau hapus user dengan role **Admin**.


#### A. Menambah Pengguna Baru

1. Klik tombol **"+ Tambah Pengguna"**
2. Isi form:
   - **Nama Lengkap** - Nama user
   - **Username** - Username untuk login (huruf, angka, underscore)
   - **ID Pegawai** - Nomor pegawai (opsional)
   - **Email** - Email user
   - **Role** - Pilih: Fuelman atau Group Leader (Supervisor tidak bisa create Supervisor/Admin baru)
   - **Password** - Password (minimal 6 karakter)
   - **Konfirmasi Password** - Ulangi password
3. Klik **"Tambah Pengguna"**

#### B. Mengedit Pengguna

1. Klik tombol **"Edit"** pada user
2. Ubah data yang diperlukan
3. Jika ingin ubah password, isi field password baru
4. Jika tidak ubah password, kosongkan field password
5. Klik **"Simpan Perubahan"**

**Batasan:**
- Supervisor hanya bisa edit **diri sendiri**, tidak bisa edit Supervisor lain
- Supervisor tidak bisa edit user dengan role **Admin**

#### C. Menghapus Pengguna

1. Klik tombol **"Hapus"** pada user
2. Konfirmasi penghapusan

**Batasan:**
- Tidak bisa hapus diri sendiri
- Tidak bisa hapus Supervisor lain
- Tidak bisa hapus Admin

### 5.7 Tips untuk Supervisor

**✅ Best Practices:**

1. **Review Berkala** - Cek dashboard setiap hari untuk memastikan tidak ada pending approval
2. **Kelola Master Data** - Update kalibrasi tangki secara berkala jika ada perubahan
3. **Manajemen User** - Nonaktifkan atau hapus user yang sudah tidak bekerja
4. **Backup Data** - Pastikan data laporan di-backup secara berkala
5. **Monitoring** - Gunakan fitur rekap & analisis untuk monitoring penggunaan BBM

---


## BAB 6: PANDUAN PENGGUNA - ROLE ADMIN

### 6.1 Gambaran Umum Role Admin

**Admin** adalah super user dengan akses penuh ke sistem kecuali approval laporan. Admin bertanggung jawab atas konfigurasi sistem dan manajemen user tingkat lanjut.

**Hak Akses Admin:**
- ✅ Melihat semua laporan dari semua site
- ✅ Dashboard dengan statistik lengkap semua status
- ✅ Akses ke Rekap & Analisis BBM
- ✅ Akses ke Monitoring Tangki
- ✅ **Kelola Tangki** - Full access (Create, Edit, Delete)
- ✅ **Kelola Site** - Full access (Create, Edit, Delete)
- ✅ **Manajemen Pengguna** - Full access termasuk create/edit **Supervisor dan Admin**
- ❌ **Tidak bisa Approve/Reject Laporan** - Hanya Supervisor yang bisa

**Perbedaan Admin vs Supervisor:**

| Fitur | Supervisor | Admin |
|-------|-----------|-------|
| Approve Laporan | ✅ Bisa | ❌ Tidak Bisa |
| Edit Semua User | ❌ Tidak Bisa | ✅ Bisa |
| Create Supervisor | ❌ Tidak Bisa | ✅ Bisa |
| Edit Supervisor Lain | ❌ Tidak Bisa | ✅ Bisa |
| Lihat User Admin | ❌ Tidak Bisa | ✅ Bisa |

**Catatan Penting:**
Sistem hanya boleh memiliki **1 Admin**. Admin tidak bisa membuat Admin baru jika sudah ada Admin lain di sistem.


### 6.2 Dashboard Admin

Dashboard Admin menampilkan statistik lengkap:

- **Draft** - Total laporan dengan status draft
- **Menunggu Verifikasi GL** - Total laporan submitted
- **Diverifikasi GL** - Total laporan verified
- **Disetujui SPV** - Total laporan approved
- **Ditolak** - Total laporan rejected

Dashboard ini memberikan overview lengkap kondisi semua laporan di sistem.

### 6.3 Manajemen Pengguna (Admin)

Admin memiliki kontrol penuh atas manajemen user.

**Cara Akses:**
- Klik menu **"Manajemen Pengguna"** di sidebar

**Daftar Pengguna:**
Admin dapat melihat **semua user** termasuk Admin sendiri, berbeda dengan Supervisor yang tidak bisa melihat Admin.

#### A. Menambah Pengguna Baru

1. Klik tombol **"+ Tambah Pengguna"**
2. Isi form (sama seperti Supervisor)
3. **Role yang Tersedia:**
   - Fuelman
   - Group Leader
   - **Supervisor** ✅ (Admin bisa create Supervisor)
   - **Admin** ⚠️ (Hanya muncul jika belum ada Admin lain)

4. Klik **"Tambah Pengguna"**

**Catatan Admin Role:**
Jika sudah ada Admin di sistem, opsi "Admin" tidak akan muncul di dropdown dan akan ada catatan:
> *"Admin sudah ada. Sistem hanya dapat memiliki 1 Admin."*


#### B. Mengedit Pengguna

1. Klik tombol **"Edit"** pada user manapun
2. Ubah data yang diperlukan
3. **Admin bisa:**
   - Edit **semua user** (Fuelman, GL, Supervisor, termasuk Admin lain jika ada)
   - Ubah role user (termasuk promote Fuelman → GL → Supervisor)
   - Reset password user

4. Klik **"Simpan Perubahan"**

**Hati-hati:**
- Jika Admin mengubah role dirinya sendiri ke role lain (Supervisor/GL/Fuelman), dia akan kehilangan akses Admin
- Pastikan ada Admin lain atau jangan ubah role Admin yang sedang login

#### C. Menghapus Pengguna

1. Klik tombol **"Hapus"** pada user
2. Konfirmasi penghapusan

Admin bisa hapus **semua user kecuali diri sendiri**.

### 6.4 Kelola Tangki dan Site (Admin)

Fitur Kelola Tangki dan Kelola Site untuk Admin **sama persis** dengan Supervisor (lihat Bab 5.4 dan 5.5).

Admin memiliki full access untuk:
- Create, Edit, Delete tangki
- Upload dan update kalibrasi tangki
- Create, Edit, Delete site
- Mengaktifkan/nonaktifkan tangki atau site

### 6.5 Tips untuk Admin

**✅ Best Practices:**

1. **Jangan Ubah Role Sendiri** - Bisa kehilangan akses Admin
2. **Backup Credentials** - Simpan username/password Admin dengan aman
3. **Regular Audit** - Cek daftar user secara berkala, hapus yang tidak aktif
4. **Master Data** - Pastikan data tangki dan kalibrasi selalu up-to-date
5. **Monitor System** - Gunakan dashboard untuk monitoring aktivitas sistem

**❌ Hal yang Harus Dihindari:**

1. Jangan share password Admin ke siapapun
2. Jangan create Admin lebih dari 1 (sistem tidak mengizinkan)
3. Jangan hapus user yang masih aktif tanpa koordinasi
4. Jangan ubah kalibrasi tangki tanpa dokumen pendukung

---


## BAB 7: MANFAAT DAN SARAN

### 7.1 Manfaat Aplikasi Daily Report

#### A. Manfaat untuk Organisasi

1. **Efisiensi Operasional**
   - Mengurangi waktu pembuatan laporan hingga 60%
   - Proses approval yang lebih cepat (dari 2-3 hari menjadi beberapa jam)
   - Eliminasi duplikasi data entry

2. **Akurasi Data**
   - Validasi otomatis mengurangi human error
   - Kalibrasi tangki terintegrasi memastikan konversi sounding-liter yang akurat
   - Perhitungan flow meter otomatis menghindari kesalahan hitung

3. **Transparansi dan Akuntabilitas**
   - Audit trail lengkap untuk setiap perubahan
   - Tracking siapa, kapan, dan apa yang diubah
   - Feedback tertulis untuk setiap penolakan

4. **Analisis Data**
   - Dashboard real-time untuk monitoring
   - Rekap bulanan otomatis
   - Trend analysis untuk pengambilan keputusan

5. **Penghematan Biaya**
   - Paperless system menghemat biaya kertas dan printer
   - Cloud storage lebih murah daripada filing fisik
   - Mengurangi waktu administrasi

#### B. Manfaat untuk Fuelman

- 📱 Input data lebih cepat dan mudah
- 🔔 Notifikasi jika laporan ditolak
- 📊 Dapat melihat riwayat laporan sendiri
- ✅ Validasi data instant (tidak perlu tunggu feedback)
- 📸 Foto terorganisir dengan baik

#### C. Manfaat untuk Group Leader

- ⚡ Verifikasi laporan lebih cepat
- 📈 Dashboard monitoring untuk prioritas kerja
- 💬 Feedback terstruktur ke Fuelman
- 📊 Akses ke rekap dan analisis


#### D. Manfaat untuk Supervisor

- 🎯 Fokus pada laporan yang sudah diverifikasi GL
- 📊 Analytics untuk decision making
- 🔧 Kontrol penuh atas konfigurasi sistem
- 👥 Manajemen user terpusat

#### E. Manfaat untuk Admin

- 🛠️ Konfigurasi sistem fleksibel
- 👁️ Visibility penuh ke semua aspek sistem
- 📋 Audit dan compliance management
- 🔐 Security dan access control

### 7.2 Saran Pengembangan di Masa Depan

#### A. Fitur yang Direkomendasikan

1. **Export dan Reporting**
   - Export laporan ke PDF dengan format resmi
   - Export rekap bulanan ke Excel
   - Scheduled email report otomatis

2. **Mobile Application**
   - Aplikasi Android untuk Fuelman
   - Kamera terintegrasi untuk foto sounding
   - Offline mode dengan sync otomatis

3. **Notifikasi Push**
   - Email notification saat laporan ditolak
   - WhatsApp notification untuk urgent action
   - SMS notification untuk deadline reminder

4. **Advanced Analytics**
   - Predictive analytics untuk kebutuhan BBM
   - Anomaly detection untuk data mencurigakan
   - Grafik trend multi-periode

5. **Integration**
   - API untuk integrasi dengan sistem lain
   - Integration dengan sistem procurement
   - Integration dengan sistem inventory management


6. **Security Enhancements**
   - Two-factor authentication (2FA)
   - Biometric login untuk mobile app
   - Session timeout otomatis
   - IP whitelisting untuk admin access

7. **Workflow Improvements**
   - Bulk approve untuk multiple reports
   - Delegasi approval ke backup GL/SPV
   - Scheduled reports (auto-create draft setiap hari)
   - Template untuk laporan rutin

8. **Data Management**
   - Automatic backup ke cloud storage
   - Data archival untuk laporan lama
   - Data retention policy
   - Soft delete dengan restore capability

#### B. Saran Operasional

1. **Training dan Sosialisasi**
   - Regular training untuk user baru
   - Video tutorial untuk setiap fitur
   - Quick reference guide yang mudah diakses
   - Support team untuk troubleshooting

2. **Standard Operating Procedure (SOP)**
   - SOP untuk setiap role user
   - Deadline untuk setiap tahap approval
   - Escalation procedure jika ada delay
   - Incident response procedure

3. **Quality Assurance**
   - Periodic audit terhadap data laporan
   - Review proses approval secara berkala
   - User feedback collection
   - Continuous improvement program

4. **Backup dan Disaster Recovery**
   - Daily backup otomatis
   - Off-site backup storage
   - Disaster recovery plan
   - Regular backup testing


### 7.3 Best Practices Penggunaan Aplikasi

#### A. Untuk Semua User

1. **Keamanan Akun**
   - Gunakan password yang kuat (kombinasi huruf, angka, simbol)
   - Jangan share password ke orang lain
   - Logout setelah selesai menggunakan aplikasi
   - Update password secara berkala (3-6 bulan sekali)

2. **Input Data**
   - Input data sesegera mungkin setelah pengukuran
   - Double check data sebelum submit
   - Gunakan fitur keterangan untuk informasi tambahan
   - Upload foto yang jelas dan relevan

3. **Komunikasi**
   - Baca feedback dengan teliti
   - Tanyakan jika ada yang tidak jelas
   - Koordinasi dengan atasan jika ada masalah
   - Laporkan bug atau error ke admin

#### B. Workflow yang Efisien

1. **Fuelman:**
   - Submit laporan sebelum jam 3 sore
   - Cek dashboard setiap pagi untuk laporan yang ditolak
   - Simpan sebagai draft jika belum lengkap

2. **Group Leader:**
   - Verifikasi laporan maksimal H+1
   - Prioritaskan laporan tertua
   - Berikan feedback yang konstruktif dan jelas

3. **Supervisor:**
   - Approve laporan maksimal H+1 setelah verified
   - Review rekap mingguan untuk monitoring
   - Update master data secara berkala

4. **Admin:**
   - Audit user access setiap bulan
   - Monitor system performance
   - Backup data secara berkala


### 7.4 Troubleshooting Umum

#### A. Masalah Login

**Problem:** Tidak bisa login dengan username/email dan password

**Solusi:**
1. Pastikan username/email dan password ditulis dengan benar (case-sensitive)
2. Coba gunakan email jika username tidak berhasil, atau sebaliknya
3. Clear browser cache dan cookies
4. Coba browser lain
5. Hubungi admin untuk reset password jika lupa

#### B. Liter Menampilkan "XXXX"

**Problem:** Setelah input sounding, liter menampilkan "XXXX"

**Penyebab:** Sounding yang diinput di luar range kalibrasi tangki

**Solusi:**
1. Cek ulang pengukuran sounding di lapangan
2. Pastikan tidak ada kesalahan baca (misalnya 125 dibaca 225)
3. Jika sounding memang benar, hubungi Supervisor untuk update kalibrasi tangki

#### C. Foto Tidak Bisa Di-Upload

**Problem:** Upload foto gagal atau loading terus

**Solusi:**
1. Cek ukuran foto (maksimal 5MB per foto)
2. Cek format foto (harus JPG, JPEG, atau PNG)
3. Compress foto jika terlalu besar
4. Cek koneksi internet
5. Coba upload satu per satu jika upload sekaligus gagal

#### D. Data Tidak Tersimpan

**Problem:** Setelah klik "Simpan", data tidak tersimpan

**Solusi:**
1. Cek apakah ada pesan error merah di atas form
2. Pastikan semua field required sudah diisi
3. Cek koneksi internet
4. Jangan close tab browser sebelum muncul notifikasi sukses
5. Screenshot error dan kirim ke admin


#### E. Laporan Tidak Bisa Di-Edit

**Problem:** Tombol "Ubah" tidak muncul atau disabled

**Penyebab:** Laporan dengan status tertentu tidak bisa diedit

**Penjelasan:**
- ✅ **Draft** - Bisa diedit
- ❌ **Submitted** - Tidak bisa diedit (sudah masuk proses approval)
- ❌ **Verified** - Tidak bisa diedit (menunggu SPV)
- ❌ **Approved** - Tidak bisa diedit (sudah final)
- ✅ **Rejected** - Bisa diedit (untuk revisi)

**Solusi:** 
Jika laporan sudah submitted dan ada kesalahan, minta GL/SPV untuk reject agar bisa diedit kembali.

#### F. Performance Lambat

**Problem:** Aplikasi loading lama atau lambat

**Solusi:**
1. Cek koneksi internet
2. Clear browser cache
3. Tutup tab browser yang tidak digunakan
4. Restart browser
5. Gunakan browser modern (Chrome, Firefox, Edge)
6. Hindari upload banyak foto sekaligus

### 7.5 Kontak dan Dukungan

**Untuk Bantuan Teknis:**

- **Admin Sistem**: [Nama Admin] - [Email/Phone]
- **IT Support**: [Email IT Support]
- **Emergency Contact**: [Phone Emergency]

**Jam Operasional Support:**
- Senin - Jumat: 08:00 - 17:00 WIB
- Sabtu: 08:00 - 12:00 WIB
- Minggu & Libur: Emergency only

**Cara Melaporkan Bug atau Masalah:**

1. Screenshot error message atau masalah
2. Catat langkah-langkah yang menyebabkan error
3. Email ke admin dengan subject: "[BUG] Deskripsi Singkat"
4. Sertakan informasi:
   - Browser yang digunakan
   - Waktu kejadian
   - Screenshot
   - Role user Anda

---


## PENUTUP

### Ucapan Terima Kasih

Terima kasih kepada semua pihak yang telah berkontribusi dalam pengembangan dan implementasi Aplikasi Daily Report ini:

**Tim Pengembang**
- Untuk dedikasi dan kerja keras dalam membangun sistem yang reliable dan user-friendly
- Untuk kesabaran dalam menerima feedback dan melakukan iterasi improvement

**Management**
- Untuk support dan kepercayaan dalam digitalisasi proses pelaporan
- Untuk alokasi resources yang memadai untuk pengembangan aplikasi

**Tim Operasional**
- **Fuelman** - Yang telah beradaptasi dengan sistem baru dan memberikan feedback berharga
- **Group Leader** - Yang telah membantu proses verifikasi dengan teliti dan cepat
- **Supervisor** - Yang telah mengelola sistem dengan baik dan memberikan arahan strategis

**User Awal (Early Adopters)**
- Untuk kesediaan menjadi pilot project
- Untuk feedback dan bug report yang sangat membantu penyempurnaan sistem
- Untuk kesabaran menghadapi issues di fase awal implementasi

**Stakeholder dan End Users**
- Untuk kepercayaan menggunakan sistem ini dalam operasional sehari-hari
- Untuk komitmen dalam menjaga kualitas data
- Untuk kontribusi dalam continuous improvement

### Pesan Penutup

Aplikasi Daily Report ini adalah hasil dari kolaborasi berbagai pihak dengan tujuan meningkatkan efisiensi dan akurasi dalam pelaporan harian kegiatan Fuelman. Sistem ini akan terus dikembangkan dan disempurnakan berdasarkan kebutuhan operasional dan feedback dari pengguna.

**Komitmen Kami:**
- 🎯 Continuous improvement berdasarkan feedback user
- 🔒 Menjaga keamanan dan integritas data
- ⚡ Meningkatkan performance dan reliability sistem
- 📚 Menyediakan training dan support yang memadai
- 🚀 Mengembangkan fitur baru yang bermanfaat


**Mari Bersama Membangun Sistem yang Lebih Baik:**

Kesuksesan aplikasi ini sangat bergantung pada partisipasi aktif dari seluruh pengguna. Kami mengajak Anda untuk:

- 💬 Memberikan feedback dan saran perbaikan
- 🐛 Melaporkan bug atau error yang ditemukan
- 📖 Membaca manual ini dengan seksama
- 🤝 Membantu rekan kerja yang kesulitan menggunakan sistem
- 🎓 Mengikuti training dan update fitur baru

**Ingatlah:**

> *"Teknologi adalah alat, tetapi manusia adalah kunci kesuksesan implementasinya."*

Dengan komitmen bersama untuk menggunakan sistem ini dengan baik dan benar, kita dapat mencapai tujuan bersama: **operasional yang lebih efisien, data yang lebih akurat, dan pekerjaan yang lebih mudah**.

---

### Informasi Dokumen

**Judul:** Manual Aplikasi Daily Report - Sistem Pelaporan Harian Kegiatan Fuelman

**Versi:** 1.0

**Tanggal Publikasi:** Januari 2025

**Penyusun:** Tim Pengembang Daily Report System

**Status:** Final

**Revisi Terakhir:** -

---

### Riwayat Perubahan

| Versi | Tanggal | Perubahan | Penyusun |
|-------|---------|-----------|----------|
| 1.0 | Jan 2025 | Dokumen awal | Tim Pengembang |

---

**© 2025 Daily Report System - Warehouse & Inventory**

*Dokumen ini bersifat confidential dan hanya untuk penggunaan internal.*

---

## LAMPIRAN

### A. Glossary (Istilah)

- **Sounding** - Pengukuran ketinggian BBM dalam tangki menggunakan stik ukur (dalam cm)
- **Flow Meter** - Alat ukur volume BBM yang keluar dari tangki (dalam liter)
- **FM Pakai** - Selisih pembacaan flow meter sore dikurangi pagi (penggunaan harian)
- **Kalibrasi** - Tabel konversi dari sounding (cm) ke volume (liter)
- **Main Hole** - Lubang ukur utama pada tangki (TENGAH, DEPAN, BELAKANG)
- **SPM** - Storage Petroleum Material (nama tangki)
- **Draft** - Status laporan yang belum disubmit
- **Submitted** - Status laporan yang sudah disubmit ke GL
- **Verified** - Status laporan yang sudah diverifikasi GL
- **Approved** - Status laporan yang sudah disetujui SPV (final)
- **Rejected** - Status laporan yang ditolak, perlu revisi
- **GL** - Group Leader
- **SPV** - Supervisor


### B. Workflow Diagram

```
WORKFLOW PELAPORAN HARIAN:

┌─────────────┐
│  FUELMAN    │
│  Buat Draft │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│   Submit    │
│  (Submitted)│
└──────┬──────┘
       │
       ▼
┌─────────────┐      ┌──────────┐
│ GROUP LEADER│─────▶│  Reject  │
│  Verifikasi │      │(Feedback)│
└──────┬──────┘      └────┬─────┘
       │                  │
       ▼                  │
┌─────────────┐           │
│  Verified   │           │
└──────┬──────┘           │
       │                  │
       ▼                  │
┌─────────────┐      ┌───▼──────┐
│ SUPERVISOR  │─────▶│  Reject  │
│   Approve   │      │(Feedback)│
└──────┬──────┘      └────┬─────┘
       │                  │
       ▼                  │
┌─────────────┐           │
│  Approved   │           │
│   (FINAL)   │           │
└─────────────┘           │
                          │
       ┌──────────────────┘
       │
       ▼
┌─────────────┐
│  FUELMAN    │
│    Revisi   │
└──────┬──────┘
       │
       └─────▶ (Kembali ke Submit)
```

### C. Role & Permission Matrix

| Fitur | Fuelman | Group Leader | Supervisor | Admin |
|-------|---------|--------------|------------|-------|
| **Dashboard** | ✅ | ✅ | ✅ | ✅ |
| **Buat Laporan** | ✅ | ❌ | ❌ | ❌ |
| **Lihat Laporan Sendiri** | ✅ | ❌ | ❌ | ❌ |
| **Lihat Semua Laporan** | ❌ | ✅ | ✅ | ✅ |
| **Edit Draft/Rejected** | ✅ | ❌ | ❌ | ❌ |
| **Submit Laporan** | ✅ | ❌ | ❌ | ❌ |
| **Verifikasi Laporan** | ❌ | ✅ | ❌ | ❌ |
| **Approve Laporan** | ❌ | ❌ | ✅ | ❌ |
| **Reject Laporan** | ❌ | ✅ | ✅ | ❌ |
| **Rekap & Analisis** | ❌ | ✅ | ✅ | ✅ |
| **Monitoring Tangki** | ❌ | ✅ | ✅ | ✅ |
| **Kelola Tangki** | ❌ | ❌ | ✅ | ✅ |
| **Kelola Site** | ❌ | ❌ | ✅ | ✅ |
| **Manajemen User** | ❌ | ❌ | ✅* | ✅** |

*) Supervisor: Tidak bisa kelola Admin/SPV lain
**) Admin: Full access


### D. FAQ (Frequently Asked Questions)

**Q1: Berapa maksimal foto yang bisa di-upload?**

A: Tergantung jenis tangki:
- Tangki Main Hole TENGAH: 8 foto
- Tangki Main Hole DEPAN/BELAKANG: 4 foto per row
- Transfer Solar: 6 foto

**Q2: Apakah bisa edit laporan setelah di-submit?**

A: Tidak bisa. Setelah submit, laporan masuk workflow approval dan locked. Hanya bisa diedit jika laporan direject oleh GL/SPV.

**Q3: Bagaimana jika lupa password?**

A: Hubungi Admin untuk reset password. Admin dapat membuat password baru untuk Anda.

**Q4: Apakah bisa login dari HP?**

A: Ya, aplikasi responsive dan bisa diakses dari browser HP. Namun untuk pengalaman terbaik, disarankan menggunakan laptop/PC.

**Q5: Berapa lama data laporan disimpan?**

A: Data laporan disimpan permanen di database. Tidak ada auto-delete.

**Q6: Apakah bisa membuat laporan untuk tanggal kemarin?**

A: Ya, bisa pilih tanggal manual saat membuat laporan. Namun sebaiknya buat laporan di hari yang sama.

**Q7: Kenapa liter menampilkan XXXX?**

A: Karena sounding yang diinput di luar range kalibrasi tangki. Cek ulang pengukuran atau hubungi Supervisor untuk update kalibrasi.

**Q8: Apakah Supervisor bisa menolak laporan yang sudah diverifikasi GL?**

A: Ya, Supervisor masih bisa menolak laporan yang sudah diverifikasi GL jika menemukan kesalahan.

**Q9: Berapa lama waktu yang direkomendasikan untuk approval?**

A: 
- GL: Maksimal H+1 setelah submit
- SPV: Maksimal H+1 setelah verified

**Q10: Apakah ada limit jumlah laporan yang bisa dibuat per hari?**

A: Tidak ada limit. Tapi biasanya 1 site = 1 laporan per hari.


### E. Keyboard Shortcuts

| Shortcut | Fungsi |
|----------|--------|
| `Ctrl + S` | Simpan draft (di halaman create/edit) |
| `Ctrl + Enter` | Submit laporan (di halaman create/edit) |
| `Esc` | Tutup modal/dialog |
| `Tab` | Pindah ke field berikutnya |
| `Shift + Tab` | Pindah ke field sebelumnya |

### F. Browser yang Disarankan

**Recommended (Fully Supported):**
- ✅ Google Chrome 90+
- ✅ Mozilla Firefox 88+
- ✅ Microsoft Edge 90+
- ✅ Safari 14+

**Not Recommended:**
- ❌ Internet Explorer (tidak didukung)
- ❌ Browser versi lama

**Tips:**
- Selalu update browser ke versi terbaru
- Enable JavaScript
- Allow cookies dari aplikasi
- Gunakan incognito/private mode jika bermasalah dengan cache

### G. System Requirements

**Minimum:**
- OS: Windows 7, macOS 10.12, atau Linux modern
- RAM: 2GB
- Browser: Chrome/Firefox/Edge versi 2 tahun terakhir
- Internet: 1 Mbps

**Recommended:**
- OS: Windows 10/11, macOS 11+, atau Linux modern
- RAM: 4GB atau lebih
- Browser: Chrome/Firefox/Edge versi terbaru
- Internet: 5 Mbps atau lebih cepat
- Screen: Minimal 1366x768 resolution

---

### H. Tips Fotografi untuk Laporan

**Foto Sounding:**
- 📸 Fokus pada stik sounding dengan angka terbaca jelas
- 💡 Pencahayaan cukup, tidak blur
- 📏 Tampilkan angka sounding dengan jelas
- ✅ Hindari shadow atau pantulan yang menutupi angka

**Foto Flow Meter:**
- 📸 Close-up pada display angka
- 💡 Pastikan semua digit terbaca
- 📅 Sertakan indikator waktu jika ada
- ✅ Foto horizontal lebih baik daripada vertikal

**Foto Transfer:**
- 📸 Foto proses transfer sedang berlangsung
- 💡 Tampilkan tangki sumber dan tujuan jika memungkinkan
- 📊 Foto meter atau display volume
- ✅ Multiple angle untuk dokumentasi lengkap

---

**AKHIR DOKUMEN**

---

*Untuk pertanyaan lebih lanjut atau feedback terkait manual ini, silakan hubungi tim admin atau IT support.*

*Terima kasih telah menggunakan Aplikasi Daily Report!*
