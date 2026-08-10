<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\DailyReport;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'customer_id',
        'name',
        'country',
        'username',
        'email',
        'phone',
        'category',
        'status',
        'approval_status',
        'rejection_reason',
        'joining_date',
        'address',
    ];

    /**
     * Customer belongs to Client.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Customer / Host has many Daily Reports.
     */
    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }
}