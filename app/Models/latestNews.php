<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class latestNews extends Model
{
    use HasFactory;

    // Define the fillable attributes
    protected $fillable = [
        'latest_news_date',
        'image',
        'description',
        'category',
        'campaign_id',  // Add this if not already present
        'zakat_id',      // Add this if not already present
        'infak_id',      // Add this if not already present
        'wakaf_id',      // Add this if not already present
    ];
    

    // Define the relationships with other models
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
