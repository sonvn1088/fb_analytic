<?php

namespace App\Models;

use App\Helps\Facebook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use FormatTime;
    use SoftDeletes;

    const ADMIN = 1;
    const EDITOR = 2;
    const BM = 3;

    const YES = 1;
    const NO = 0;

    const ACTIVE = 1;
    const INACTIVE = 2;
    const DISABlED = 0;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'accounts';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'username', 'email', 'birthday', 'fb_id', 'gender', 'token',
        'password', 'group_id', 'role', 'status', 'on_server', 'profile', 'app_id', 'app_token', 'friend_with', 'browser_id', 'friends'];


    public $roles = [1 => 'Admin', 2 => 'Editor', 3 => 'BM'];


    public $yesNo = [1 => 'Yes', 0 => 'No'];

    public $statuses = [1 => 'Active', 0 => 'Disabled', 2 => 'Inactive'];

    /**
     * Get the group that owns the account.
     */
    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }

    /**
     * Get the browser that owns the account.
     */
    public function browser()
    {
        return $this->belongsTo('App\Models\Browser');
    }

    /**
     * Get the app that owns the account.
     */
    public function app()
    {
        return $this->belongsTo('App\Models\App');
    }

    public function getStatusAttribute(){
        if(isset($this->attributes['status']))
            return  ['value' => $this->attributes['status'], 'label' => Arr::get($this->statuses, $this->attributes['status'])];
        else
            return  ['value' => null, 'label' => null];
    }

    public function getRoleAttribute(){
        if($this->attributes['role'])
            return  ['value' => $this->attributes['role'], 'label' => Arr::get($this->roles, $this->attributes['role'])];
        else
            return  ['value' => '', 'label' => ''];
    }


    public function getOnServerAttribute(){
        return  ['value' => $this->attributes['on_server'], 'label' => Arr::get($this->yesNo, $this->attributes['on_server'])];
    }


    public function setPagesToken($isFullToken = true){
        $pages = Facebook::getPages($isFullToken?$this->token:$this->app_token);

        $myPages = MyPage::where('group_id', $this->group->id)
            ->get();

        foreach($myPages as $myPage){
            $myPage->token = Arr::get($pages, $myPage->fb_id.'.access_token');
            $myPage->save();

            echo $myPage->name.': '.$myPage->token. '<br>';
        }
        return;
    }
}
