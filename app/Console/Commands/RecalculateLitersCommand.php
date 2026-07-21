<?php

namespace App\Console\Commands;

use App\Models\DailyReport;
use App\Models\Tank;
use Illuminate\Console\Command;

class RecalculateLitersCommand extends Command
{
    protected $signature = 'reports:recalculate-liters {--report-id=}';
    protected $description = 'Recalculate liter values from sounding using tank calibration data';

    public function handle()
    {
        $reportId = $this->option('report-id');
        
        if ($reportId) {
            $reports = DailyReport::where('id', $reportId)->get();
            if ($reports->isEmpty()) {
                $this->error("Report with ID {$reportId} not found.");
                return 1;
            }
        } else {
            $reports = DailyReport::all();
        }

        $this->info("Recalculating liter values for " . $reports->count() . " report(s)...");
        
        $updatedItems = 0;
        $updatedTransfers = 0;

        foreach ($reports as $report) {
            $this->line("Processing Report #{$report->id} - {$report->date->format('Y-m-d')}");
            
            // Recalculate items
            foreach ($report->items as $item) {
                $tank = Tank::find($item->tank_id);
                if (!$tank) {
                    continue;
                }

                $updated = false;
                
                // Recalculate liter_pagi from sounding_pagi
                if ($item->sounding_pagi !== null) {
                    $newLiterPagi = $tank->soundingToLiter($item->sounding_pagi);
                    if ($newLiterPagi !== null && $newLiterPagi != $item->liter_pagi) {
                        $item->liter_pagi = $newLiterPagi;
                        $updated = true;
                    }
                }
                
                // Recalculate liter_sore from sounding_sore
                if ($item->sounding_sore !== null) {
                    $newLiterSore = $tank->soundingToLiter($item->sounding_sore);
                    if ($newLiterSore !== null && $newLiterSore != $item->liter_sore) {
                        $item->liter_sore = $newLiterSore;
                        $updated = true;
                    }
                }
                
                if ($updated) {
                    $item->save();
                    $updatedItems++;
                    $this->line("  ✓ Updated {$tank->code} - {$tank->main_hole}");
                }
            }
            
            // Recalculate transfers
            foreach ($report->transfers as $transfer) {
                $updated = false;
                
                // SPM liter
                if ($transfer->spm_hasil !== null && !empty($transfer->dari_tangki)) {
                    $spmTank = Tank::where('code', $transfer->dari_tangki)->first();
                    if ($spmTank) {
                        $newSpmLiter = $spmTank->soundingToLiter(abs($transfer->spm_hasil));
                        if ($newSpmLiter !== null && $newSpmLiter != $transfer->spm_liter) {
                            $transfer->spm_liter = $newSpmLiter;
                            $updated = true;
                        }
                    }
                }
                
                // FT liter
                if ($transfer->ft_hasil !== null && !empty($transfer->ke_tangki)) {
                    $ftTank = Tank::where('code', $transfer->ke_tangki)->first();
                    if ($ftTank) {
                        $newFtLiter = $ftTank->soundingToLiter(abs($transfer->ft_hasil));
                        if ($newFtLiter !== null && $newFtLiter != $transfer->ft_liter) {
                            $transfer->ft_liter = $newFtLiter;
                            $updated = true;
                        }
                    }
                }
                
                if ($updated) {
                    $transfer->save();
                    $updatedTransfers++;
                    $this->line("  ✓ Updated transfer {$transfer->dari_tangki} -> {$transfer->ke_tangki}");
                }
            }
        }

        $this->info("\nRecalculation complete!");
        $this->info("Updated {$updatedItems} item(s)");
        $this->info("Updated {$updatedTransfers} transfer(s)");
        
        return 0;
    }
}
