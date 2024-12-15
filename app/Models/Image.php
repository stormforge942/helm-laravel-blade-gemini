<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'alt_text',
        'filename',
        'niche_id',
        'title'
    ];

    protected $appends = [
        'url',
    ];

    public function niche()
    {
        return $this->belongsTo(NicheContent::class);
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->filename);
    }

    public function getAltTextAttribute($value)
    {
        return $value != 'null' ? $value : '';
    }
}
