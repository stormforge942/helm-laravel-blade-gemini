<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Server;
use App\Models\WpApiDetail;
use App\Models\User;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_name',
        'site_url',
        'db_name',
        'db_username',
        'db_password',
        'wp_prefix',
        'server',
        'niche'
    ];
    protected $table = 'wordpress_sites';

    public function getDbNameAttribute(string $value)
    {
        return decrypt($value);
    }

    public function setDbNameAttribute(string $value)
    {
        $this->attributes['db_name']  = encrypt($value);
    }

    public function getDbUsernameAttribute(string $value)
    {
        return decrypt($value);
    }

    public function setDbUsernameAttribute(string $value)
    {
        $this->attributes['db_username']  = encrypt($value);
    }

    public function getDbPasswordAttribute(string $value)
    {
        return decrypt($value);
    }

    public function setDbPasswordAttribute(string $value)
    {
        $this->attributes['db_password']  = encrypt($value);
    }

    public function getWpPrefixAttribute(string $value)
    {
        return decrypt($value);
    }

    public function setWpPrefixAttribute(string $value)
    {
        $this->attributes['wp_prefix']  = encrypt($value);
    }

    public function server() {
        return $this->belongsTo(Server::class, 'server', 'name');
    }

    public function wpApiDetails()
    {
        return $this->hasOne(WpApiDetail::class, 'site_url', 'site_url');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'restricted_user_sites' );
    }
}
