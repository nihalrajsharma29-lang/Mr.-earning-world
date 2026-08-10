<?php

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DailyReportImport;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Ensure test clients exist and adjust CSV client IDs to actual IDs
echo "Preparing test clients...\n";

/** @var \App\Models\Client $c1 */
$c1 = App\Models\Client::firstOrCreate(
    ['email' => 'client1@example.test'],
    ['name' => 'Test Client 1', 'phone' => '1111111111', 'company' => 'TestCo']
);

/** @var \App\Models\Client $c2 */
$c2 = App\Models\Client::firstOrCreate(
    ['email' => 'client2@example.test'],
    ['name' => 'Test Client 2', 'phone' => '2222222222', 'company' => 'TestCo']
);

$csvPath = storage_path('app/imports/test_daily_reports.csv');
$csv = file_get_contents($csvPath);
$csv = preg_replace('/,1,/', ',' . $c1->id . ',', $csv, 1);
$csv = preg_replace('/,2,/', ',' . $c2->id . ',', $csv, 1);
file_put_contents($csvPath, $csv);

$path = $csvPath;

echo "Importing: {$path}\n";

try {
    Excel::import(new DailyReportImport, $path);
    echo "Import finished.\n";
    $count = App\Models\DailyReport::count();
    echo "Total DailyReport rows: {$count}\n";
    $recent = App\Models\DailyReport::latest('id')->take(5)->get();
    foreach ($recent as $r) {
        echo "#{$r->id} date={$r->dt} host_id={$r->host_id} client_id={$r->client_id} total_coins={$r->total_coins}\n";
    }
} catch (Throwable $e) {
    echo "Import failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
