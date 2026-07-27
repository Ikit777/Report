-- Fix main_hole_variant for existing SPM45 data
-- This assumes the 3 rows are consecutive in ID order

-- For report_id 33 (from screenshot), tank SPM45
-- You need to find the actual item IDs first

-- Step 1: Check current data
SELECT 
    dri.id,
    dri.daily_report_id,
    t.code as tank_code,
    t.main_hole,
    dri.main_hole_variant,
    dri.sounding_pagi
FROM daily_report_items dri
JOIN tanks t ON dri.tank_id = t.id
WHERE t.main_hole = '(DEPAN + BELAKANG) / 2'
    AND dri.main_hole_variant IS NULL
ORDER BY dri.daily_report_id, dri.id;

-- Step 2: Update based on the IDs from step 1
-- Replace the IDs with actual IDs from your query result

-- For each report, assuming IDs are consecutive:
-- UPDATE daily_report_items SET main_hole_variant = 'DEPAN' WHERE id = <first_id>;
-- UPDATE daily_report_items SET main_hole_variant = 'BELAKANG' WHERE id = <second_id>;
-- UPDATE daily_report_items SET main_hole_variant = '(DEPAN + BELAKANG) / 2' WHERE id = <third_id>;
