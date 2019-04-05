<?php

namespace App\Helps;

use App\Models\Token;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Facebook
{
    static public function getPageInfo($id){
        $fields = 'name,username,fan_count';
        return self::get($id, ['fields' => $fields], self::getToken()->token);
    }

    static public function getPostInfo($id){
        $fields = 'permalink_url,link,created_time,name,type,message';
        return self::get($id, ['fields' => $fields], self::getToken()->token);
    }

    static public function getPosts($page_id, $since){
        $fields = 'permalink_url,link,created_time,name,type,message';
        return self::get($page_id.'/feed', ['fields' => $fields, 'since' => $since], self::getToken()->token);
    }

    static public function getEngagement($post_id){
        $fields = 'likes,comments,shares';
        return self::get($post_id, ['fields' => $fields], self::getToken()->token);
    }

    static public function getFriendsTaggedPhotos($userId, $token){
        $friends = self::getFriends($userId, $token);

        foreach($friends as $friend){
            $photos = self::getUserTaggedPhotos($friend['id'], $token);
            $imagesData = [];
            foreach($photos as $photo){
                $image = end($photo['images']);
                $imagesData[] = [
                    'id' => $photo['id'],
                    'source' => $image['source'],
                    'created_time' => date('Y-m-d H:i:s')
                ];
            }


            $friendsPhotos[] = [
                'id' => $friend['id'],
                'name' => $friend['name'],
                'data' => $imagesData,
            ];
        }

        return $friendsPhotos;
    }

    static public function getFriends($id, $token){
        $params = [
            'fields' => 'birthday,email,name,gender',
            'limit' => 5000
        ];
        return self::get($id.'/friends', $params, $token);
    }

    function getUser($userId, $token){
        $params = [
            'fields' => 'birthday,email,name,gender',
        ];
        return self::get($userId, $params, $token);
    }

    function getUserTaggedPhotos($userId, $token){
        $params = [
            'type' => 'tagged',
            'fields' => 'name,images',
        ];
        return self::get($userId.'/photos', $params, $token);
    }

    static public function get($uri, $params, $token){
        $limit = $params['limit']??100;
        $client = new Client();
        $query = $params;
        $query['access_token'] = $token;
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
