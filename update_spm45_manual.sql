-- SCRIPT UNTUK UPDATE MAIN_HOLE_VARIANT UNTUK LAPORAN ID 33
-- Jalankan di Railway PostgreSQL Console atau melalui php artisan db

-- Lihat data sekarang
SELECT id, tank_id, main_hole_variant, sounding_pagi 
FROM daily_report_items 
WHERE daily_report_id = 33 
ORDER BY id;

-- Update berdasarkan urutan ID (sesuaikan dengan ID yang muncul di atas)
-- GANTI <ID1>, <ID2>, <ID3> dengan ID sebenarnya dari query di atas

-- Contoh: jika ID nya 100, 101, 102, maka:
-- UPDATE daily_report_items SET main_hole_variant = 'DEPAN' WHERE id = 100;
-- UPDATE daily_report_items SET main_hole_variant = 'BELAKANG' WHERE id = 101;
-- UPDATE daily_report_items SET main_hole_variant = '(DEPAN + BELAKANG) / 2' WHERE id = 102;

-- Verify hasil update
SELECT id, tank_id, main_hole_variant, sounding_pagi 
FROM daily_report_items 
WHERE daily_report_id = 33 
ORDER BY id;
