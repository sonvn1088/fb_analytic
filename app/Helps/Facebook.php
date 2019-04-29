<?php

namespace App\Helps;

use App\Models\Account;
use App\Models\Browser;
use App\Models\Token;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class Facebook
{
    static public function getPageInfo($id){
        $fields = 'name,username,fan_count';
        return self::get($id, ['fields' => $fields], self::getRandomToken());
    }

    static public function getPostInfo($id){
        $fields = 'permalink_url,link,created_time,name,type,message';
        return self::get($id, ['fields' => $fields], self::getRandomToken());
    }

    static public function getPosts($pageId, $since){
        $fields = 'permalink_url,link,created_time,name,type,message,full_picture';
        return self::get($pageId.'/feed', ['fields' => $fields, 'since' => $since], self::getRandomToken());
    }

    static public function getEngagement($postId){
        $fields = 'likes,comments,shares';
        return self::get($postId, ['fields' => $fields], self::getRandomToken());
    }

    static public function getPublishedPosts($token, $since, $until = null, $limit = 200){
        $fields = 'permalink_url,link,created_time,full_picture,message,description,name,caption,type,attachments';
        $params = [
            'fields' => $fields,
            'limit' => $limit,
        ];
        if($since)
            $params['since'] = $since;
        if($until)
            $params['until'] = $until;


        $posts = self::get('me/feed', $params, $token);

        foreach($posts as $id => $post){
            if(!isset($post['message']) || !$post['message']){
                self::delete($id, $token);
            }

            //delete post null picture
            if(!isset($post['full_picture']) || !$post['full_picture']){
                self::delete($id, $token);
            }
        }

        return $posts;
    }

    static public function getScheduledPosts2($token){
        $result = Facebook::get('me', ['fields' => 'scheduled_posts'], $token);
        return Arr::get($result, 'scheduled_posts.data');
    }

    static public function getScheduledPosts($token, $limit = 200){
        $params = [
            'fields' => 'permalink_url,link,created_time,scheduled_publish_time,full_picture,message,description,name,caption,type,attachments',
            'is_published' => false,
            'limit' => $limit,
        ];

        /*$posts = self::get('me/scheduled_posts', $params, $token);
        if(Arr::has($posts, 'error')){
            $result = Facebook::get('me', ['fields' => 'scheduled_posts'], $token);
            $nextUrl = Arr::get($result, 'scheduled_posts.paging.next');
            if($nextUrl){
                $parts = parse_url($nextUrl);
                $token = $parts['access_token'];
            }
        }*/
        $posts = self::get('me/scheduled_posts', $params, $token);

        foreach($posts as $id => $post){
            if(!isset($post['scheduled_publish_time'])){
                unset($posts[$id]);
            }

            if(!isset($post['message']) || !$post['message']){
                self::delete($id, $token);
            }

            //delete post null picture
            if(!isset($post['full_picture']) || !$post['full_picture']){
                self::delete($id, $token);
            }
        }

        uasort($posts, function($a, $b) {
            return $a['scheduled_publish_time'] - $b['scheduled_publish_time'];
        });

        return $posts;
    }

    static public function getPages($token){
        $params = [
            'fields' => 'name,access_token,username',
            'limit' => 25,
        ];

        return self::get('me/accounts', $params, $token);
    }

    static public function getPageInfos($pageId){
        $client = new Client(['verify' => false ]);
        $response = $client->get('https://www.facebook.com/'.$pageId);
        $content = $response->getBody()->getContents();

        $regexPattern = "/<div>(.{0,12}) người thích trang này<\/div>/";
        preg_match($regexPattern, $content, $match);
        $info['like'] = str_replace('.', '', $match[1]??0);

        $regexPattern = "/<div>(.{0,12}) người theo dõi trang này<\/div>/";
        preg_match($regexPattern, $content, $match);
        $info['follow'] = str_replace('.', '', $match[1]??0);

        $regexPattern = "/<title id=\"pageTitle\">(.*?) -.*?<\/title>/";
        preg_match($regexPattern, $content, $match);
        $info['name'] = str_replace('&#039;', "'", $match[1]??'');

        $regexPattern = "/\"username\":\"(.*?)\"/";
        preg_match($regexPattern, $content, $match);
        $info['username'] = $match[1]??'';

        $regexPattern = "/content=\"fb:\/\/page\/\?id=(.*?)\"/";
        preg_match($regexPattern, $content, $match);
        $info['fb_id'] = $match[1]??'';

        return $info;
    }

    static public function getFriendsTaggedPhotos($userId, $token){
        $friends = self::getFriends($userId, $token);
        foreach($friends as $friend){
            $photos = self::getUserTaggedPhotos($friend['id'], $token);
            if(count($photos) < 1){
                $photos = self::getUserProfilePhotos($friend['id'], $token);
            }
            $imagesData = [];
            $i = 0;
            foreach($photos as $photo){
                $i++;
                $images = Arr::get($photo, 'images', []);
                $image = end($images);
                if(!empty($image))
                    $imagesData[$photo['id']] = str_replace(config('facebook.cdn'), '', $image['source']);
            }


            $friendsPhotos[$friend['id']] = [
                $friend['name'],
                $imagesData,
            ];
        }

        return $friendsPhotos;
    }

    static public function getFriends($id, $token){
        $params = [
            'fields' => 'birthday,email,name,first_name,last_name,middle_name,gender',
            'limit' => 5000
        ];
        return self::get($id.'/friends', $params, $token);
    }

    static public function getUser($userId, $token){
        $params = [
            'fields' => 'gender,first_name,middle_name,last_name,birthday,hometown{location},email,link,location{location},friends.limit(0),groups.limit(0)',
        ];
        return self::get($userId, $params, $token);
    }

    static public function getUserTaggedPhotos($userId, $token){
        $params = [
            'type' => 'tagged',
            'fields' => 'name,images',
            'limit' => 15
        ];
        return self::get($userId.'/photos', $params, $token);
    }

    static public function getUserProfilePhotos($userId, $token){
        $albums = self::get($userId.'/albums', ['fields' => 'name'], $token);
        foreach($albums as $album) {
            if ($album['name'] == 'Profile Pictures') {
                $photos = self::get($album['id'].'/photos', ['fields' => 'name,images', 'limit' => 15], $token);
                return $photos;
            }
        }
        return [];
    }

    static public function checkToken($token){
        return self::get('me', [], $token);
    }

    static public function sharePosts($posts, $token, $stepTime){
        $scheduledPosts = self::getScheduledPosts($token);
        /*print_r($scheduledPosts);die();
        if(empty($scheduledPosts)){
            $scheduledPosts = self::getScheduledPosts2($token);
            print_r($scheduledPosts);die('xxxx');
        }*/

        $publishedPosts = self::getPublishedPosts($token, time() - 24*3600);


        //check block reach
        $count = 0;
        foreach($publishedPosts as $publishedPost){
            if($publishedPost['created_time'] >= gmdate('Y-m-d', time() - 3600).'T'.gmdate('H:i:s', time() - 3600))
                $count++;
        }


        if(count($scheduledPosts) < 2 && $count > 60/config('facebook.step_time')){
            return;
        }

        $postedUris = self::deleteDuplicatedPosts($token, $scheduledPosts, $publishedPosts);
        $firstScheduledTime = self::getNextScheduledTime($token, $scheduledPosts, $stepTime);

        $i = 0;
        foreach($posts as $id => $post){
            $params = $post;
            $uri = $uri = parse_url($post['link'], PHP_URL_PATH);

            if(!in_array($uri, $postedUris)){
                $params['message'] = str_replace('&#039;', "'", html_entity_decode($params['message']));

                if($stepTime == 'now'){
                    $params['published'] = true;
                }else{
                    $params['published'] = false;
                    $params['scheduled_publish_time'] =  $firstScheduledTime + $i*$stepTime*60;
                }

                $params['feed_targeting'] = ['age_min' => 18];
                $params['targeting'] = ['age_min' => 18];

                if(isset($params['page_link'])){
                    $params['link'] = $params['page_link'];
                }

                self::post('me/feed', $params, $token);
                /* $delay = rand(20, 30);
                 sleep($delay);*/
                $i++;
            }
        }
    }

    /*
     * Get next scheduled time
     *
     * @param $scheduledPosts array
     * @param $stepTime int
     *
     * @return int
     */
    static function getNextScheduledTime($token, $scheduledPosts, $stepTime){
        self::reschedulePosts($token, $scheduledPosts, $stepTime);

        $current_time = floor(time()/300)*300+300; //round to 5 mins

        if(empty($scheduledPosts))
            return $current_time+($stepTime > 10?$stepTime:10)*60;
        else {

            $lastScheduledPost = end($scheduledPosts);
            $lastScheduledTime = $lastScheduledPost['scheduled_publish_time'];
            if($lastScheduledTime < time())
                return $current_time+($stepTime > 10?$stepTime:10)*60;

            return $lastScheduledTime + $stepTime*60;
        }

    }

    /*
     * Reschedule posts
     *
     * @param $page_token string
     * @param $step_time int
     */
    static public function reschedulePosts($token, $scheduledPosts = null, $stepTime){
        if(!$scheduledPosts)
            $scheduledPosts = self::getScheduledPosts($token);

        if(count($scheduledPosts) > 0){
            $rootTime = floor(time()/300)*300+300; //round to 5 mins
            $is_current = true;
            foreach($scheduledPosts as $postId => $scheduledPost){
                if($scheduledPost['scheduled_publish_time'] >= time()){
                    $rootTime = $scheduledPost['scheduled_publish_time'] > $rootTime + 10*60?
                        $rootTime + 10*60:$scheduledPost['scheduled_publish_time'];
                    $is_current = false;
                    break;
                }else {
                    self::delete($postId, $token); //delete unpublish post
                }
            }

            $i = 0;
            foreach($scheduledPosts as $postId => $scheduledPost){
                if($scheduledPost['scheduled_publish_time'] > time()){
                    $time = $rootTime+$i*$stepTime*60;
                    if( $scheduledPost['scheduled_publish_time'] != $time){
                        self::post($postId, ['scheduled_publish_time' => $time], $token);
                    }
                    $i++;
                }
            }

            return $rootTime+$i*$stepTime*60;
            /*foreach($scheduledPosts as $postId => $scheduledPost){
                if($scheduledPost['scheduled_publish_time'] < time() - 5*60){
                    $time = ($is_current && $i == 0)?$rootTime+10*60:$rootTime+$i*$stepTime*60;
                    self::post($postId, ['scheduled_publish_time' => $time], $token);
                    $i++;
                }
            }*/
        }
    }


    /*
     * Delete duplicated posts on fanpage
     *
     * @param $token string
     * @param $scheduledPosts array
     * @param $publishedPosts array
     *
     * @return array
     *
     */

    static public function deleteDuplicatedPosts($token, $scheduledPosts = null, $publishedPosts = null){
        $uris = [];
        foreach( ($publishedPosts + $scheduledPosts) as $postId => $post){
            $uri = self::getPostUri($post);
            if(in_array($uri, $uris)){
                self::delete($postId, $token);
            }else{
                $uris[] = $uri;
            }
        }

        return $uris;
    }

    /*
     * Get URI from post
     *
     * @param $post array
     * @return string
     */
    static public function getPostUri($post){
        $url = $post['attachments'][0]['subattachments'][0]['url']??($post['link']??'');
        return parse_url($url, PHP_URL_PATH);
    }

    /*
     * Delete a object on facebook
     *
     * @param $uri string
     * @param $token string
     *
     * @return array
     *
     */

    static public function delete($uri, $token){
        $client = new Client();
        $query['access_token'] = $token;

        try{
            $response = $client->delete(config('facebook.graph').$uri, ['form_params' => $query]);
            $result =  \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
            return $result;
        }catch (BadResponseException $e){
            return [];
        }
    }

    /*
     * Post a object on facebook
     *
     * @param $uri string
     * @param $params array
     * @param $token string
     *
     * @return array
     *
     */

    static public function post($uri, $params, $token){
        $client = new Client();
        $query = $params;
        $query['access_token'] = $token;

        try{
            $response = $client->post(config('facebook.graph').$uri, ['form_params' => $query]);
            $result =  \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
            return $result;
        }catch (BadResponseException $e){
            return ['error' => ['message' => $e->getMessage()]];
        }

    }


    /*
     * Get objects on facebook
     *
     * @param $uri string
     * @param $params array
     * @param $token string
     *
     * @return array
     *
     */
    static public function get($uri, $params, $token){
        $limit = $params['limit']??100;
        $client = new Client();
        $query = $params;
        $query['limit'] = $limit < 100?$limit:100;
        $query['access_token'] = $token;
        try{
            $response = $client->get(config('facebook.graph').$uri, ['query' => $query]);
            $result =  \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
        }catch (BadResponseException $e){
            return ['error' => ['message' => $e->getMessage(), 'code' => $e->getCode()]];
        }catch(Exception $e){
            return ['error' => ['message' => $e->getMessage(), 'code' => $e->getCode()]];
        }


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

        }

        return $result;
    }

    static public function getRandomToken(){
        $accounts = Account::where('status', Account::ACTIVE)
            ->where('role', Account::EDITOR)
            ->inRandomOrder()
            ->get();

        foreach($accounts as $account){
            $result = self::checkToken($account->app_token?:$account->token);
            if(isset($result['id']))
                return $account->token;
            else{
                $account->status = Account::INACTIVE;
                $account->save();
            }
        }
    }


    /*
     * Login facebook by app
     *
     * @param $user string
     * @param $password string
     *
     * @return array
     *
     */
    static public function generateToken($account){
        if(!$browser = $account->browser)
            $browser = new Browser(['name' => config('facebook.api.agent'), 'type' => Browser::IPHONE]);

        if($browser->type == Browser::IPAD){
            $browser = Browser::where('type', [Browser::IPHONE, Browser::ANDROID])
                ->inRandomOrder()->first();
            $account->browser_id = $browser->id;
            $account->save();
        }

        switch ($browser->type) {
            case Browser::IPHONE:
                $apiKey = config('facebook.api.iphone.key');
                $apiSecret = config('facebook.api.iphone.secret');
        break;
            case Browser::ANDROID:
                $apiKey = config('facebook.api.android.key');
                $apiSecret = config('facebook.api.android.secret');
        break;
            case Browser::IPAD:
                $apiKey = config('facebook.api.ipad.key');
                $apiSecret = config('facebook.api.ipad.secret');

        }

        $data = array(
            'api_key' => $apiKey,
            'credentials_type' => 'password',
            'email' => $account->fb_id?:$account->username,
            'format' => 'JSON',
            'generate_machine_id' => '1',
            'generate_session_cookies' => '1',
            'locale' => 'en_US',
            'method' => 'auth.login',
            'password' => $account->password,
            'return_ssl_resources' => '0',
            'v' => '1.0'
        );

        self::_signCreator($data, $apiSecret);

        $client = new Client(['verify' => false ]);
        $response = $client->get(config('facebook.api.base_url').'?'.http_build_query($data), [
            'headers'   =>  [
                'User-Agent' => $browser->name,
            ]
        ]);

        return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
    }

    /*
     * Sign data with secret
     *
     * @param $data array
     * @param $secret string
     *
     * @return array
     */
    static private function _signCreator(&$data, $secret){
        $sig = '';
        foreach($data as $key => $value){
            $sig .= "$key=$value";
        }
        $sig .= $secret;
        $sig = md5($sig);
        return $data['sig'] = $sig;
    }

}
