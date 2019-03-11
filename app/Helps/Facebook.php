<?php

namespace App\Helps;

use App\Models\Token;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Facebook
{
    static public function getPageInfo($id){
        $fields = 'name,username,fan_count';
        return self::get($id, ['fields' => $fields]);
    }

    static public function getPostInfo($id){
        $fields = 'permalink_url,link,created_time,name,type,message';
        return self::get($id, ['fields' => $fields]);
    }

    static public function getPosts($page_id, $since){
        $fields = 'permalink_url,link,created_time,name,type,message';
        return self::get($page_id.'/feed', ['fields' => $fields, 'since' => $since]);
    }

    static public function getEngagement($post_id){
        $fields = 'likes,comments,shares';
        return self::get($post_id, ['fields' => $fields]);
    }

    static public function get($uri, $params, $limit = 100){
        $client = new Client();
        $token = self::getToken();
        $query = $params;
        $query['access_token'] = $token->token;
        $response = $client->get(config('facebook.graph').$uri, ['query' => $query]);
        $result =  \GuzzleHttp\json_decode($response->getBody()->getContents(), true);

        if(isset($result['data'])){
            $items = [];
            foreach ($result['data'] as $item) {
                $items[$item['id']] = $item;
            }

            while(isset($result['paging']['next']) && count($items) < $limit){
                $response = $client->get($result['paging']['next']);
                $result = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);

                foreach ($result['data'] as $item) {
                    $items[$item['id']] = $item;
                }
            }
            return $items;
        }elseif(isset($result['error'])){
            self::get($uri, $params, $limit);
            try{
                $token->status = 0;
                $token->save();
            }catch (Exception $e){

            }
        }

        return $result;
    }

    static public function getToken(){
        $token = Token::where('status', 1)->first();
        if(!$token)
            die();
        return $token;
    }
}
