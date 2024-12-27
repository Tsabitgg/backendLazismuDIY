<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaction extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke campaign
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    // Relasi ke zakat
    public function zakat()
    {
        return $this->belongsTo(Zakat::class, 'zakat_id');
    }

    // Relasi ke infak
    public function infak()
    {
        return $this->belongsTo(Infak::class, 'infak_id');
    }

    // Relasi ke wakaf
    public function wakaf()
    {
        return $this->belongsTo(Wakaf::class, 'wakaf_id');
    }
}
