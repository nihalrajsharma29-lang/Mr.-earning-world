<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CustomerController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
    Route::resource('clients', ClientController::class);

    // Admin actions: send reset link and regenerate temporary password
    Route::post(
        '/admin/clients/{client}/send-reset',
        [ClientController::class, 'sendResetLink']
    )->name('clients.send-reset');

    Route::post(
        '/admin/clients/{client}/regen-password',
        [ClientController::class, 'regenPassword']
    )->name('clients.regen-password');
use App\Http\Controllers\Admin\DailyReportImportController as AdminDailyReportImportController;
use App\Http\Controllers\Admin\DailyReportController as AdminDailyReportController;
use App\Http\Controllers\Admin\HostApprovalController;

use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\DailyReportImportController as ClientDailyReportImportController;
use App\Http\Controllers\Client\HostAuditController;
use App\Http\Controllers\Client\DailyReportController;
use App\Http\Controllers\Client\BankCardController;

use App\Models\DailyReport;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Simple sitemap for public pages. Update list as needed.
Route::get('/sitemap.xml', function () {
    $base = config('app.url') ?: request()->getSchemeAndHttpHost();

    $urls = [
        $base . '/',
        $base . '/login',
        $base . '/forgot-password',
    ];

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($urls as $u) {
        $xml .= "  <url>\n    <loc>{$u}</loc>\n    <changefreq>daily</changefreq>\n  </url>\n";
    }

    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'application/xml');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Main Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', function () {

        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('client.dashboard');

    })->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/dashboard',
        [AdminDashboardController::class, 'index']
    )->name('admin.dashboard');


    /*
    |--------------------------------------------------------------------------
    | ADMIN - EXCEL IMPORT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/daily-reports/import',
        [AdminDailyReportImportController::class, 'create']
    )->name('admin.daily.import');

    Route::post(
        '/admin/daily-reports/import',
        [AdminDailyReportImportController::class, 'store']
    )->name('admin.daily.import.store');


    /*
    |--------------------------------------------------------------------------
    | ADMIN - VIEW REPORTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/reports',
        [AdminDailyReportController::class, 'index']
    )->name('admin.reports');


    /*
    |--------------------------------------------------------------------------
    | ADMIN - DELETE REPORTS BY DATE
    |--------------------------------------------------------------------------
    */

    Route::delete(
        '/admin/reports/date/{date}',
        [AdminDailyReportController::class, 'destroyByDate']
    )->name('admin.reports.delete.date');

    Route::delete(
        '/admin/reports/selected',
        [AdminDailyReportController::class, 'destroySelected']
    )->name('admin.reports.delete.selected');

    // ADMIN - AUDIT LOGS
    Route::get(
        '/admin/audit',
        [\App\Http\Controllers\Admin\AuditController::class, 'index']
    )->name('admin.audit');


    /*
    |--------------------------------------------------------------------------
    | ADMIN - CLIENTS
    |--------------------------------------------------------------------------
    */

    Route::resource('clients', ClientController::class);


    /*
    |--------------------------------------------------------------------------
    | ADMIN - HOST APPROVAL
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/hosts',
        [HostApprovalController::class, 'index']
    )->name('admin.hosts.index');

    Route::patch(
        '/admin/hosts/selected/approve',
        [HostApprovalController::class, 'approveSelected']
    )->name('admin.hosts.approve.selected');

    Route::delete(
        '/admin/hosts/selected',
        [HostApprovalController::class, 'destroySelected']
    )->name('admin.hosts.destroy.selected');

    Route::patch(
        '/admin/hosts/{customer}/approve',
        [HostApprovalController::class, 'approve']
    )->name('admin.hosts.approve');

    Route::patch(
        '/admin/hosts/{customer}/reject',
        [HostApprovalController::class, 'reject']
    )->name('admin.hosts.reject');

    Route::patch(
        '/admin/hosts/{customer}/reassign',
        [HostApprovalController::class, 'reassign']
    )->name('admin.hosts.reassign');

    Route::delete(
        '/admin/hosts/{customer}',
        [HostApprovalController::class, 'destroy']
    )->name('admin.hosts.destroy');


    /*
    |--------------------------------------------------------------------------
    | CLIENT DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/client/dashboard',
        [ClientDashboardController::class, 'index']
    )->name('client.dashboard');


    /*
    |--------------------------------------------------------------------------
    | CLIENT - ADD HOST
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/client/hosts/create',
        [CustomerController::class, 'create']
    )->name('client.hosts.create');

    Route::post(
        '/client/hosts',
        [CustomerController::class, 'store']
    )->name('client.hosts.store');


    /*
    |--------------------------------------------------------------------------
    | CLIENT - HOST AUDIT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/client/hosts/audit',
        [HostAuditController::class, 'index']
    )->name('client.hosts.audit');


    /*
    |--------------------------------------------------------------------------
    | CLIENT - BANK CARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/client/bank-card',
        [BankCardController::class, 'index']
    )->name('client.bank-card');

    Route::post(
        '/client/bank-card',
        [BankCardController::class, 'store']
    )->name('client.bank-card.store');

    Route::delete(
        '/admin/bank-details/{client}',
        [\App\Http\Controllers\Admin\BankDetailsController::class, 'destroy']
    )->name('admin.bank-details.destroy');

    Route::get(
        '/admin/bank-details',
        [\App\Http\Controllers\Admin\BankDetailsController::class, 'index']
    )->name('admin.bank-details');

    /*
    |--------------------------------------------------------------------------
    | CLIENT - DAILY REPORTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/client/daily-reports',
        [DailyReportController::class, 'index']
    )->name('client.daily.reports');

    Route::get(
        '/client/daily-reports/import',
        [ClientDailyReportImportController::class, 'create']
    )->name('client.daily.import');

    Route::post(
        '/client/daily-reports/import',
        [ClientDailyReportImportController::class, 'store']
    )->name('client.daily.import.store');


    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class,
        'destroy'
    ])->name('profile.destroy');

});


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';


/*
|--------------------------------------------------------------------------
| ZIP Extension Check
|--------------------------------------------------------------------------
*/

Route::get('/check-zip', function () {

    return [
        'php_version' => PHP_VERSION,
        'php_binary' => PHP_BINARY,
        'zip_loaded' => extension_loaded('zip'),
        'ziparchive' => class_exists('ZipArchive'),
        'ini' => php_ini_loaded_file(),
    ];

});