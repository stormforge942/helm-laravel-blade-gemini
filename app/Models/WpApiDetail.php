<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Site;

class WpApiDetail extends Model
{
    protected $fillable = [
        'site_url',
        'server',
        'username',
        'password',
        'application_password'
    ];

    public function setUsernameAttribute(string $value)
    {
        $this->attributes['username']  = encrypt($value);
    }

    public function setPasswordAttribute(string $value)
    {
        $this->attributes['password']  = encrypt($value);
    }

    public function setApplicationPasswordAttribute(string $value)
    {
        $this->attributes['application_password']  = encrypt($value);
    }

    public function getUsernameAttribute(string $value)
    {
        return decrypt($value);
    }

    public function getPasswordAttribute(string $value)
    {
        return decrypt($value);
    }

    public function getApplicationPasswordAttribute(string $value)
    {
        return decrypt($value);
    }

    public function wordpressSite()
    {
        return $this->belongsTo(Site::class, 'site_url', 'site_url');
    }
}
