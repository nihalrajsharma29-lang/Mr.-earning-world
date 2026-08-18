<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkippedImportId extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id',
        'report_type',
        'client_id',
        'reason',
        'source_file_name',
        'imported_by_user_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function importedBy()
    {
        return $this->belongsTo(User::class, 'imported_by_user_id');
    }
}