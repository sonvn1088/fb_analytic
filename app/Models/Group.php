<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groups';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Get the accounts record associated with the group.
     */
    public function accounts()
    {
        return $this->hasMany('App\Models\Account');
    }

    /**
     * Get the accounts record associated with the group.
     */
    public function pages()
    {
        return $this->hasMany('App\Models\MyPage');
    }
}
