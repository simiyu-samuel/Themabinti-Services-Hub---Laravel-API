<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'customer_email',
        'preferred_date_time',
        'message',
        'admin_notes',
        'status',
        'assigned_seller_id',
    ];

    protected $casts = [
        'preferred_date_time' => 'datetime',
    ];

    public function assignedSeller()
    {
        return $this->belongsTo(User::class, 'assigned_seller_id');
    }
}