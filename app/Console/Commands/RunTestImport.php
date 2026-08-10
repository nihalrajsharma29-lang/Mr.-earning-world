<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DailyReportImport;
use App\Models\DailyReport;

class RunTestImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run test import from storage/app/imports/test_daily_reports.csv';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = storage_path('app/imports/test_daily_reports.csv');

        if (!file_exists($path)) {
            $this->error('Test CSV not found: ' . $path);
            return 1;
        }

        $this->info('Starting import from: ' . $path);

        try {
            Excel::import(new DailyReportImport, $path);
        } catch (\Throwable $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return 1;
        }

        $count = DailyReport::count();
        $this->info("Import complete. Total DailyReport rows: {$count}");

        $recent = DailyReport::latest('id')->take(5)->get();
        foreach ($recent as $r) {
            $this->line("#{$r->id} date={$r->dt} host_id={$r->host_id} client_id={$r->client_id} total_coins={$r->total_coins}");
        }

        return 0;
    }
}
