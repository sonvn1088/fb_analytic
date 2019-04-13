<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Browser extends Model
{
    const ANDROID = 1;
    const IPHONE = 2;
    const IPAD = 3;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'browsers';

    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'type'];
}
