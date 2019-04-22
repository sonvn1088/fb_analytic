<?php

namespace App\Models;

use App\Helps\Facebook;
use Illuminate\Database\Eloquent\Model;


class MyPage extends Model
{
    use FormatTime, FormatStatus;


    const ENABLED = 1;
    const DISABlED = 0;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'my_pages';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'fb_id', 'like', 'follow', 'status', 'group_id', 'site_ids', 'username', 'blocked_at'];


    protected $casts = [
        'like' => 'int',
        'follow' => 'int',
        'site_ids' => 'array'
    ];

    /**
     * Get the group that owns the page.
     */
    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }

    public function sites(){
        return Site::whereIn('id', $this->site_ids)
            ->where('status', 1)
            ->get();
    }

    public function editor(){
        return Account::where('group_id', $this->group_id)
            ->where('role', Account::EDITOR)
            ->where('status', Account::ACTIVE)
            ->first();
    }

    public function sharePosts(){
        if($this->status['value'] == 1){
            Facebook::sharePosts($this->_getPosts(), $this->token, config('facebook.step_time'));
        }
    }

    private function _getPosts(){
        $items = [];
        foreach($this->sites() as $site){
            $posts = json_decode(file_get_contents($site->path), true);
            if(is_array($posts))
                foreach($posts as $post){
                    $message = $this->_getMessageFromPost($post['messages'], $post['description'], $post['content']);

                    $items[] = [
                        'link' => $post['link'],
                        'message' => $message,
                    ];
                }
        }

        $shuffled_items = [];
        $ids = array_keys($items);
        shuffle($ids);
        foreach ($ids as $id) {
            $shuffled_items[$id] = $items[$id];
        }

        return $shuffled_items;
    }

    private function _getMessageFromPost($messages, $description, $content){
        if(is_array($messages) && count($messages) >= 2){
            return $messages[array_rand($messages)];
        }
        $messages = [];
        if($description)
            $messages[] = $description;


        $regexPattern = "/<p class=\"text\">(.*?)<\/p>/";
        preg_match_all($regexPattern, $content, $matches);
        $texts = $matches[1];

        $regexPattern = "/<span class=\"caption\">(.*?)<\/span>/";
        preg_match_all($regexPattern, $content, $matches);
        $texts = array_merge($texts, $matches[1]);


        foreach($texts as $part){
            if(strpos($part, 'http') === false){
                $part = strip_tags($part);
                $regexPattern = "/\. ([A-Z])/";
                $part = preg_replace($regexPattern, '||$1', $part);
                $sentences = explode('||', $part);
                foreach($sentences as $sentence){
                    if(strlen(trim($sentence)) > 50)
                        $messages[] = $sentence.'...';
                }
            }
        }

        return $messages[array_rand($messages)];
    }
}
