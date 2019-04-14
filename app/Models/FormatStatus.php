<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Support\Arr;

trait FormatStatus
{
    public $statuses = [1 => 'Enabled', 0 => 'Disabled'];

    public function getStatusAttribute(){
        if(isset($this->attributes['status']))
            return  ['value' => $this->attributes['status'], 'label' => Arr::get($this->statuses, $this->attributes['status'])];
        else
            return  ['value' => null, 'label' => null];
    }
}
