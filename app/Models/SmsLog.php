<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * This is the model class for table "{{table}}".
 */
class SmsLog extends Model
{

    const ACTION_SEND = 1;

    const ACTION_CHECK = 2;

    protected $table = 'sms_logs';

    protected $fillable = [
        'id',
        'data',
        'action',
        'created_at',
        'updated_at',
        'phone',
    ];
}
