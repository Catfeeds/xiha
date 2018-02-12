<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayLog extends Model
{
    protected $table = 'pay_log';

    protected $primaryKey = 'id';

    protected $dateFormat = 'U';

    protected $guarded = [];
}
