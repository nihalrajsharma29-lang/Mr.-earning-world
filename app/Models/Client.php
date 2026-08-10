<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address',
        'status',
        'user_id',
    ];

    /**
     * Client belongs to a User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Client has many Customers / Hosts.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Client has many Daily Reports.
     */
    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }
}