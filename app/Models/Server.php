<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Site;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address'
    ];
    protected $table = 'servers';

    public function wordpress_sites() {
        return $this->hasMany(Site::class, 'server', 'name');
    }
}
