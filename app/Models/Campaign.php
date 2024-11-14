<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_category_id',
        'campaign_name',
        'campaign_code',
        'campaign_thumbnail',
        'campaign_image_1',
        'campaign_image_2',
        'campaign_image_3',
        'description',
        'location',
        'target_amount',
        'start_date',
        'active',
        'approved',
    ];

    public function category()
    {
        return $this->belongsTo(CampaignCategory::class, 'campaign_category_id');
    }

}
