<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'apps';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'secret', 'name'];
}
