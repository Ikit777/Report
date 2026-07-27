<?php

// Script to fix SPM45 main_hole_variant for existing data
// Run: php fix_spm45_data.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Fixing SPM45 main_hole_variant data...\n\n";

// Find all daily_report_items for tanks with main_hole = "(DEPAN + BELAKANG) / 2"
$items = DB::table('daily_report_items')
    ->join('tanks', 'daily_report_items.tank_id', '=', 'tanks.id')
    ->where('tanks.main_hole', '(DEPAN + BELAKANG) / 2')
    ->whereNull('daily_report_items.main_hole_variant')
    ->select('daily_report_items.*', 'tanks.code as tank_code', 'tanks.main_hole')
    ->orderBy('daily_report_items.daily_report_id')
    ->orderBy('daily_report_items.id')
    ->get();

echo "Found " . $items->count() . " items to fix\n\n";

// Group by daily_report_id and tank_id
$grouped = $items->groupBy(function($item) {
    return $item->daily_report_id . '_' . $item->tank_id;
});

$fixedCount = 0;

foreach ($grouped as $key => $group) {
    if ($group->count() !== 3) {
        echo "Skipping group $key - expected 3 rows, got " . $group->count() . "\n";
        continue;
    }
    
    echo "Fixing group $key (Report #{$group[0]->daily_report_id}, Tank: {$group[0]->tank_code}):\n";
    
    // Set variant for each row
    $variants = ['DEPAN', 'BELAKANG', '(DEPAN + BELAKANG) / 2'];
    
    foreach ($group as $index => $item) {
        $variant = $variants[$index];
        DB::table('daily_report_items')
            ->where('id', $item->id)
            ->update(['main_hole_variant' => $variant]);
        
        echo "  - Row {$item->id}: set to '{$variant}'\n";
        $fixedCount++;
    }
    
    echo "\n";
}

echo "Done! Fixed $fixedCount rows.\n";
