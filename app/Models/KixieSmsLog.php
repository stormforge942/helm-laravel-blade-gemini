<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KixieSmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'messageid',
        'from',
        'customernumber',
        'to',
        'businessnumber',
        'direction',
        'message',
        'businessid',
        'userid',
        'deal_stage',
        'deal_title',
        'deal_value',
        'deal_status',
        'contact_firstName',
        'contact_lastName',
        'contact_address',
        'contact_phone',
        'contact_city',
        'contact_name',
        'contact_link',
        'contact_id',
        'contact_email',
        'contact_success',
        'device_type',
        'device_usecase',
        'device_isactive',
        'device_pushid',
        'crmlink',
        'email'
    ];
}
