<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Support\Arr;

trait Format
{
    public $statuses = [1 => 'Enabled', 0 => 'Disabled'];

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

    public function getBlockedAtAttribute($date)
    {
        if($date)
            return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format(config('general.format_time'));
    }

    public function getStatusAttribute(){
        if(isset($this->attributes['status']))
            return  ['value' => $this->attributes['status'], 'label' => Arr::get($this->statuses, $this->attributes['status'])];
        else
            return  ['value' => null, 'label' => null];
    }
}
