<?php

namespace App\Models;

use App\Enums\CallConstants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Call extends Model
{
    protected $fillable = [
        'callid',
        'datetime',
        'type',
        'status',
        'client_phone',
        'user_pbx',
        'diversion_phone',
        'duration',
        'wait',
        'link_record_pbx',
        'link_record_crm',
        'transcribation',
        'from_source_name'
    ];

//    protected $appends = [
//        'status_name',
//        'type_name',
//    ];

//    public function getDatetimeAttribute(): string
//    {
//        return Carbon::parse($this->attributes['datetime'])
//            ->timezone(config('app.timezone'))
//            ->toDateTimeString();
//    }


    public function getStatusNameAttribute(): string
    {
        return CallConstants::localizedStatuses($this->status);
    }

    public function getTypeNameAttribute(): string
    {
        return CallConstants::localizedTypes($this->type);
    }

}
