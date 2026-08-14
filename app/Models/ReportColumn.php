<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportColumn extends Model
{
    protected $fillable = ['report_type', 'column_key', 'label', 'type', 'position', 'is_visible'];

    protected function casts(): array
    {
        return ['is_visible' => 'boolean'];
    }
}