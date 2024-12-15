<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Corcel\Model\Post as Corcel;

class Neighborhood extends Corcel
{
    protected $postType = 'neighborhoods';
    protected $connection = 'wordpress';

    protected $fillable = [
        'post_title',
        'post_content',
        'post_status',
    ];

    protected $appends = [
        'custom_repeater_data',
        'map_repeater_data',
        'keywords_repeater_data',
        'weather_code',
        'custom_things_data'
    ];

    // public function getCustomRepeaterDataAttribute()
    // {
    //     return $this->getMeta('custom_repeater_data');
    // }

    // public function getMapRepeaterDataAttribute()
    // {
    //     return $this->getMeta('map_repeater_data');
    // }

    // public function getKeywordsRepeaterDataAttribute()
    // {
    //     return $this->getMeta('keywords_repeater_data');
    // }

    // public function getWeatherCodeAtAttribute()
    // {
    //     return $this->getMeta('weather_code');
    // }

    // public function getCustomThingsDataAttribute()
    // {
    //     return $this->getMeta('custom_things_data');
    // }

    // public function getMeta($key)
    // {
    //     $meta = get_post_meta($this->ID, $key, true);
    //     return $meta ? $meta : [];
    // }
    
}
