<?php

namespace App\Helps;

use App\Models\Link;
use App\Models\Post;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Links
{
    static public function getTopLinks($type){
        $posts = DB::table('posts')
            ->leftJoin('pages', 'pages.fb_id', '=', 'posts.page_id')
            ->leftJoin('links', 'links.id', '=', 'posts.link_id')
            ->select(DB::raw("posts.link_id, group_concat(message SEPARATOR '||') as messages,".
                "count(*) as total_page, sum(after_15) as e_after_15, sum(after_30) as e_after_30, sum(after_45) as e_after_45,".
                "sum(follow) as t_follow, sum(after_15)/sum(follow)*100000 as rate_15, sum(after_30)/sum(follow)*100000 as rate_30,".
                "sum(after_45)/sum(follow)*100000 as rate_45"))
            ->groupBy('posts.link_id')
            ->havingRaw(DB::raw("sum(follow) > 1000000 AND  sum(after_15)/sum(follow)*1000000 > 15".
                " AND sum(after_45)/sum(follow)*1000000 > 45"))
            ->where('posts.created_at', '>', date('Y-m-d H:i:s', time()-24*3600))
            ->where('pages.type', $type)
            ->get();


        $link_ids = [];
        $messages = [];
        foreach($posts as $post){
            $link_ids[] = $post->link_id;
            $messages[$post->link_id] = array_unique(explode('||', $post->messages));
        }

        $links = Link::whereIn('id', $link_ids)->get();

        //print_r($links);die();
        foreach($links as $link){
            $ignored = false;
            foreach(config('facebook.ignored_domains') as $ignoredDomain){
                if(strpos($link->url, $ignoredDomain))
                    $ignored = true;
            }

            $path = parse_url($link->url, PHP_URL_PATH);
            if(strlen($path) < 3)
                $ignored = true;


            if(!$ignored){
                if(!$link->content){
                    $article = General::parseArticle($link->url, $type);
                    $link->excerpt = Arr::get($article, 'excerpt');
                    $link->content = Arr::get($article, 'content');
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
        }

        return $result;
    }
}
