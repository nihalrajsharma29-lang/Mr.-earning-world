<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAuditLog extends Model
{
    protected $table = 'admin_audit_logs';

    protected $fillable = [
        'admin_id',
        'client_id',
        'action',
        'details',
        'ip',
        'user_agent',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
