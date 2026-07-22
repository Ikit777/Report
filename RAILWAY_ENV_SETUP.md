# Railway Environment Variables Setup

Railway menjalankan setiap service secara terpisah; `docker-compose.yml` tidak
otomatis membuat MinIO di service Laravel. Secara default, foto laporan akan
disimpan di disk `public` aplikasi agar upload tetap berjalan.

Untuk penyimpanan foto permanen, pasang Railway Volume pada `/var/www/storage`
atau tambahkan service MinIO terpisah. Panduan lengkap MinIO ada di
`RAILWAY_MULTI_SERVICE_SETUP.md`.

Setelah domain API MinIO tersedia, misalnya
`https://minio-production-xxxx.up.railway.app`, buka **Variables** pada
service Laravel dan isi:

```
FILESYSTEM_DISK=s3
REPORT_ATTACHMENT_DISK=s3
AWS_ACCESS_KEY_ID=<MINIO_ROOT_USER>
AWS_SECRET_ACCESS_KEY=<MINIO_ROOT_PASSWORD>
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=daily-report
AWS_ENDPOINT=http://${{minio.RAILWAY_PRIVATE_DOMAIN}}:9000
AWS_URL=https://${{minio.RAILWAY_PUBLIC_DOMAIN}}/daily-report
AWS_USE_PATH_STYLE_ENDPOINT=true
AWS_CONNECT_TIMEOUT=5
AWS_REQUEST_TIMEOUT=20
```

Ganti namespace `minio` jika nama service MinIO Anda berbeda. Tambahkan
Railway Volume pada service MinIO dengan mount path `/data`, lalu buat bucket
`daily-report` dan atur akses baca publik agar foto dapat ditampilkan.

Jika belum memakai MinIO, jangan set `REPORT_ATTACHMENT_DISK=s3`; biarkan
nilainya `public`.

Jangan gunakan `S3_PUBLIC_URL=https://daily-report.up.railway.app/storage`
sebagai endpoint MinIO: itu adalah domain aplikasi Laravel, bukan API object
storage, sehingga upload foto akan menunggu koneksi dan akhirnya gagal.
