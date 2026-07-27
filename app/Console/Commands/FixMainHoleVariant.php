<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tank;
use App\Models\DailyReportItem;
use Illuminate\Support\Facades\DB;

class FixMainHoleVariant extends Command
{
    protected $signature = 'fix:main-hole-variant {--report-id=}';
    protected $description = 'Fix main_hole_variant for existing SPM45/SPM3 data';

    public function handle()
    {
        $this->info('Fixing main_hole_variant for existing data...');
        
        // Find tanks with (DEPAN + BELAKANG) / 2
        $tanks = Tank::where('main_hole', '(DEPAN + BELAKANG) / 2')->get();
        
        if ($tanks->isEmpty()) {
            $this->warn('No tanks found with main_hole = "(DEPAN + BELAKANG) / 2"');
            return 0;
        }
        
        $this->info('Found ' . $tanks->count() . ' tanks: ' . $tanks->pluck('code')->join(', '));
        
        $tankIds = $tanks->pluck('id');
        
        // Get items query
        $query = DailyReportItem::whereIn('tank_id', $tankIds)
            ->whereNull('main_hole_variant');
        
        if ($this->option('report-id')) {
            $query->where('daily_report_id', $this->option('report-id'));
        }
        
        $items = $query->orderBy('daily_report_id')->orderBy('id')->get();
        
        $this->info('Found ' . $items->count() . ' items to fix');
        
        if ($items->isEmpty()) {
            $this->info('No items to fix.');
            return 0;
        }
        
        // Group by daily_report_id and tank_id
        $grouped = $items->groupBy(fn($item) => $item->daily_report_id . '_' . $item->tank_id);
        
        $fixedCount = 0;
        $variants = ['DEPAN', 'BELAKANG', '(DEPAN + BELAKANG) / 2'];
        
        foreach ($grouped as $key => $group) {
            if ($group->count() !== 3) {
                $this->warn("Skipping group $key - expected 3 rows, got " . $group->count());
                continue;
            }
            
            $tank = $tanks->firstWhere('id', $group[0]->tank_id);
            $this->info("Fixing Report #{$group[0]->daily_report_id}, Tank: {$tank->code}");
            
            foreach ($group as $index => $item) {
                $variant = $variants[$index];
                $item->main_hole_variant = $variant;
                $item->save();
                
                $this->line("  ✓ Item {$item->id}: {$variant}");
                $fixedCount++;
            }
        }
        
        $this->info("\nDone! Fixed $fixedCount rows.");
        
        return 0;
    }
}
