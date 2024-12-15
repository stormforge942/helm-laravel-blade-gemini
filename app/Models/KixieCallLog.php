<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KixieCallLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'callid',
        'businessid',
        'calldate',
        'fromnumber',
        'tonumber',
        'duration',
        'amount',
        'externalid',
        'calltype',
        'callstatus',
        'recordingurl',
        'recordingsid',
        'tonumber164',
        'fromnumber164',
        'disposition',
        'fname',
        'lname',
        'calleridName',
        'email',
        'destinationName',
        'cadenceactionprocessid',
        'powerlistid',
        'HScalltype',
        'powerlistsessionid',
        'extensionDial',
        'toExt',
        'fromExt',
        'answerDate',
        'callEndDate',
        'externalcrmid',
        'crmlink',
        'contactid',
        'dealid',
        'webhookurl',
        'outcome'
    ];
}
