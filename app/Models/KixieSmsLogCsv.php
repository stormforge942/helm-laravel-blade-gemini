<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KixieSmsLogCsv extends Model
{
    use HasFactory;
    
    protected $table = 'kixie_sms_logs_csv';

    
    protected $fillable = [
        'date',
        'first_name',
        'last_name',
        'type',
        'internal_sms_id',
        'external_contact',
        'message',
        'crm_link',
        'status',
    ];
}
