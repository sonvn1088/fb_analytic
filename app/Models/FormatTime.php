<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Support\Arr;

trait FormatTime
{

    public function getCreatedAtAttribute($date)
    {

        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format(config('general.format_time'));
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format(config('general.format_time'));
    }

    public function getBackupAttribute($date)
    {
        if($date)
            return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format(config('general.format_time'));
    }

    public function getScannedAttribute($date)
    {
        if($date)
            return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format(config('general.format_time'));
    }

    public function getBlockedAtAttribute($date)
    {
        if($date)
            return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format(config('general.format_time'));
    }
}
