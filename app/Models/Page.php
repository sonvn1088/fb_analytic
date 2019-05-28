<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    const VN = 1;
    const TH = 2;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pages';


    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'username', 'fb_id', 'like', 'follow', 'type'];
}
