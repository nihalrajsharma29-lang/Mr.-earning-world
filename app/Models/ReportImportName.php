<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportImportName extends Model
{
    protected $fillable = [
        'report_type',
        'name',
    ];

    public static function values(): array
    {
        $defaults = config('report_import_names', []);
        $stored = self::query()->pluck('name', 'report_type')->all();

        return array_replace($defaults, $stored);
    }
}