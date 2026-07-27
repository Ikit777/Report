<?php
// Quick fix script for Report ID 33
// Run: php fix_report_33.php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIXING REPORT ID 33 ===\n\n";

// Get items for report 33, ordered by ID
$items = DB::table('daily_report_items')
    ->where('daily_report_id', 33)
    ->orderBy('id')
    ->get(['id', 'tank_id', 'main_hole_variant', 'sounding_pagi']);

echo "Found " . $items->count() . " items:\n";
foreach ($items as $item) {
    echo "  ID: {$item->id}, Tank: {$item->tank_id}, Variant: " . ($item->main_hole_variant ?? 'NULL') . ", Sounding: " . ($item->sounding_pagi ?? 'NULL') . "\n";
}

if ($items->count() < 3) {
    echo "\nERROR: Expected at least 3 items, got " . $items->count() . "\n";
    exit(1);
}

echo "\n";

// Get the tank ID for first item
$firstTankId = $items->first()->tank_id;

// Check if it's a DEPAN+BELAKANG tank
$tank = DB::table('tanks')->where('id', $firstTankId)->first();
if (!$tank) {
    echo "ERROR: Tank not found\n";
    exit(1);
}

echo "Tank: {$tank->code}, Main Hole: {$tank->main_hole}\n\n";

if ($tank->main_hole !== '(DEPAN + BELAKANG) / 2') {
    echo "ERROR: Tank main_hole is not '(DEPAN + BELAKANG) / 2'\n";
    exit(1);
}

// Get items for this tank only
$tankItems = $items->where('tank_id', $firstTankId)->values();

if ($tankItems->count() !== 3) {
    echo "ERROR: Expected 3 items for tank {$tank->code}, got " . $tankItems->count() . "\n";
    exit(1);
}

echo "Updating main_hole_variant for 3 items...\n\n";

$variants = ['DEPAN', 'BELAKANG', '(DEPAN + BELAKANG) / 2'];

foreach ($tankItems as $index => $item) {
    $variant = $variants[$index];
    
    DB::table('daily_report_items')
        ->where('id', $item->id)
        ->update(['main_hole_variant' => $variant]);
    
    echo "✓ Updated ID {$item->id}: {$variant}\n";
}

echo "\n=== DONE! ===\n";
echo "Now refresh your browser with Cmd+Shift+R\n";
