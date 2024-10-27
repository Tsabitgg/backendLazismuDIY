<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignCategory extends Model
{
    use HasFactory;

    protected $fillable = ['campaign_category'];

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}

