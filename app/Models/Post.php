<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';


    const UPDATED_AT = null;

    protected $casts = [
        'data' => 'json'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['link_id', 'post_id', 'message', 'created_at', 'data'];

    static public function getPosts($time){
        $from = date('Y-m-d H:i:00', time() - $time*60);
        $to = date('Y-m-d H:i:59', time() - $time*60);
        $posts = Post::whereBetween('created_at', [$from, $to])->get();
        return $posts;
    }
}
