<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use FormatTime, FormatStatus;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sites';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'domain', 'path', 'status'];
}
