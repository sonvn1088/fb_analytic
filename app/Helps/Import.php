<?php

namespace App\Helps;

use App\Models\Link;
use App\Models\Post;
use App\Models\Page;
use Illuminate\Support\Arr;

class Import
{
    static public function engagements($time){
        $posts = Post::getPosts($time);
        foreach($posts as $post){
            $engagement = Facebook::getEngagement($post->page_id.'_'.$post->post_id);
            $l = $engagement['likes']['count']??0;
            $c = $engagement['comments']['count']??0;
            $s = $engagement['shares']['count']??0;

            $property = 'after_'.$time;
            $post->$property = $l + $c + $s;

            $data = $post->data;
            $data[$time] = [$l, $c, $s];
            $post->data = $data;

            try{
                $post->save();
            }catch (Exception $e){

            }
        }
    }

    static public function posts(){
        $pages = Page::all();

        foreach($pages as $page){
            $f_posts = Facebook::getPosts($page->fb_id, time() - 12*60);
            foreach($f_posts as $f_post){

                //save link
                if(Arr::get($f_post, 'type') == 'link'){
                    $url = strtok(Arr::get($f_post, 'link'), '?');
                    $url = str_replace('https', 'http', $url);
                    $data = [
                        'title' => Arr::get($f_post, 'name'),
                        'url' => $url,
                        'thumbnail' => Arr::get($f_post, 'full_picture'),
                    ];

                    try{
                        $link = Link::where('url', $url)->first();
                        if(!$link){
                            $link = new Link($data);
                            $link->save();
                        }

                        $tmp = explode('_', $f_post['id']);
                        $post = Post::firstOrNew(['post_id' => $tmp[1]]);

                        if(!$post->id){
                            $post->link_id = $link->id;
                            $post->page_id = $tmp[0];
                            $post->message = Arr::get($f_post, 'message');
                            //$post->created_at = date('Y-m-d H:i:s', strtotime($f_post['created_time']));
                        }

                        $post->save();

                    }catch (Exception $e){
                        echo $e->getMessage();
                    }
                }

                //$engagement = Facebook::getEngagement($post['id']);

            }
        }
    }

    static public function pages(){
        $pages = config('facebook.pages');
        foreach($pages as $username => $follow){
            $info = Facebook::getPageInfo($username);
            $data = [
                'name' => $info['name'],
                'username' => $info['username']??null,
                'fb_id' => $info['id'],
                'likes' => $info['fan_count'],
                'follow' => $follow
            ];

            try{
                $page = Page::where('fb_id', $info['id'])->first();
                if(!$page){
                    $page = new Page($data);
                    $page->save();
                }

            }catch (Exception $e){

            }

        }
    }
}
