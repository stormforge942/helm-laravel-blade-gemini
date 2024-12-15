<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NicheContent extends Model
{
    use HasFactory;
    protected $table = "niche_static_contents";

    protected $fillable = [
        'niche', 
        'services_content',
        'choose_us_content',
        'contact_us_content',
    ];
}
