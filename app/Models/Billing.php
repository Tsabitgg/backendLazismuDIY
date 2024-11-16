<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $table = 'billings';
    protected $primaryKey = 'billing_id';
    protected $fillable = [
        'created_time', 'user_id', 'username', 'phone_number', 
        'billing_amount', 'message', 'billing_date', 'va_number', 
        'method', 'transaction_qr_id', 'success', 'category', 
        'zakat_id', 'infak_id', 'campaign_id', 'wakaf_id'
    ];

    public function zakat()
    {
        return $this->belongsTo(Zakat::class);
    }

    public function infak()
    {
        return $this->belongsTo(Infak::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function wakaf()
    {
        return $this->belongsTo(Wakaf::class);
    }
}
