<?php

namespace App\Helps;

use App\Models\Link;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class Links
{
    static public function getTopLinks(){
        $posts = DB::table('posts')
            ->leftJoin('pages', 'pages.fb_id', '=', 'posts.page_id')
            ->leftJoin('links', 'links.id', '=', 'posts.link_id')
            ->select(DB::raw("posts.link_id, group_concat(message SEPARATOR '||') as messages,".
                "count(*) as total_page, sum(after_15) as e_after_15, sum(after_30) as e_after_30, sum(after_45) as e_after_45,".
                "sum(follow) as t_follow, sum(after_15)/sum(follow)*100000 as rate_15, sum(after_30)/sum(follow)*100000 as rate_30,".
                "sum(after_45)/sum(follow)*100000 as rate_45"))
            ->groupBy('posts.link_id')
            ->havingRaw(DB::raw("sum(follow) > 1000000 AND  sum(after_15)/sum(follow)*1000000 > 5".
                " AND sum(after_45)/sum(follow)*1000000 > 15"))
            ->where('posts.created_at', '>', date('Y-m-d H:i:s', time()-24*3600))
            ->get();


        $link_ids = [];
        $messages = [];
        foreach($posts as $post){
            $link_ids[] = $post->link_id;
            $messages[$post->link_id] = array_unique(explode('||', $post->messages));
        }

        $links = Link::whereIn('id', $link_ids)->get();

        foreach($links as $link){
            if(!$link->content){
                $article = General::parseArticle($link->url);
                $link->title = $article['title'];
                $link->excerpt = $article['excerpt'];
                $link->content = $article['content'];
                $link->save();
            }

            $result[$link->url] = [
                'title' => $link->title,
                'thumbnail' => $link->thumbnail,
                'excerpt' => $link->excerpt,
                'content' => $link->content,
                'message' => $messages[$link->id]
            ];
        }

        return $result;
    }
}
