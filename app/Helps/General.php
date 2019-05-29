<?php

namespace App\Helps;

use App\Models\Account;
use App\Models\MyPage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;


class General
{
    const SEPARATE = '|||';

    static public function sharePosts($from, $to){
        $myPages = MyPage::where('status', MyPage::ENABLED)
            ->where('id', '>=', $from)
            ->where('id', '<=', $to)
            ->get();
        foreach($myPages as $myPage){
            //Facebook::deleteVideos($myPage->token);
            $result = Facebook::checkToken($myPage->token);

            if(isset($result['id']))
                $myPage->sharePosts();
            else{
                $editor = $myPage->editor();
                if($editor){
                    $editor->status = Account::INACTIVE;
                    $editor->save();
                    $editor->error_message = Arr::get($result, 'error.message');
                    self::sendMail($editor);
                }
            }
        }
    }

    static public function openProfile($id, $url){
        exec(config('general.chrome_path').' --disable-web-security --disable-gpu --profile-directory="Profile '.$id.'" '.$url);
    }

    static public function sendMail($account){
        Mail::send('emails.reminder', ['account' => $account], function ($mail) use ($account) {
            $subject = 'Your account #'.$account->first_name . ' ' . $account->last_name . ' was inactive';
            $mail->from('hello@mfbac.com', 'MFBAC')
                ->to('sonvn1088@gmail.com', 'Son Nguyen')
                ->subject($subject);
        });

    }

    static private function _isEndArticles($p){
        $tmp = strtolower(strip_tags($p));
        //print_r($tmp.'--'.str_word_count($tmp).'--');
        $words = [
            'theo',
            'xem thêm',
            'xem thÊm',
            'xem nhiỀu',
        ];

        foreach($words as $word){
            if(strpos($tmp, $word) !== false && str_word_count(preg_replace('/[^A-Za-z0-9\. -]/', '', $tmp)) < 6)
                return true;
        }
        return false;
    }

    static private function _getMediaFile($p){
        $regexPattern = "/(\"|')http(.*?)\.(m3u8|mp4)(\?_=1)?(\"|')/";
        preg_match($regexPattern, $p, $match);
        $fileUrl = $match[2]?'http'.$match[2].'.'.$match[3]:'';
        return '[video src="'.$fileUrl.'"]';
    }

    static private function _getImage($p, $thumbnail = null){
        $regexPattern = "/<img.*?src=\"http(.*?)\"/";
        preg_match($regexPattern, $p, $match);
        if(isset($match[1])){
            if($thumbnail && $thumbnail == 'http'.$match[1])
                return;
            $caption = strip_tags($p);
            return '<p class="image"><img src="http'.$match[1].'"><span class="caption">'.$caption.'</span></p>';
        }
    }

    static private function _getIframe($p){
        $regexPattern = "/<iframe.*?src=\"(.*?)\"/";
        preg_match($regexPattern, $p, $match);
        $iframeUrl = $match[1]??'';
        if(strpos($iframeUrl, 'youtube')){
            $html = '<p class="text">'.$iframeUrl.'</p>';
        }elseif(strpos($iframeUrl, 'facebook.com')) {
            $tmp = explode('?href=', $iframeUrl);
            $facebookUrl = urldecode($tmp[1]);
            $html = '<p class="text">'.$facebookUrl.'</p>';
        }elseif($iframeUrl){
            $client = new Client();
            $response = $client->get($iframeUrl);
            $iframeContent = $response->getBody()->getContents();
            $html = self::_getMediaFile($iframeContent);
        }
        return $html;
    }

    static private function _getMeta($content){
        $content = str_replace(self::SEPARATE, '', $content);
        $regexPattern = "/<title>(.*?)<\/title>/";
        preg_match($regexPattern, $content, $match);
        $title = Arr::get($match, 1);

        foreach(['-', '|'] as $sep){
            $tmp = explode(" $sep ", $title);
            $last = Arr::last($tmp);
            if(is_array($tmp) && count($tmp) > 1 && count($last) < 3)
                array_pop($tmp);
            $title = implode(" $sep ", $tmp);
        }


        $regexPattern = "/<meta property=\"og:description\" content=\"(.*?)\" ?\/>/";
        preg_match($regexPattern, $content, $match);
        $excerpt = Arr::get($match, 1);

        $regexPattern = "/<meta property=\"og:image\" content=\"(.*?)\" ?\/>/";
        preg_match($regexPattern, $content, $match);
        $thumbnail = Arr::get($match, 1);
        if(!$thumbnail){
            $regexPattern = "/<meta content='https(.{50,200})' name='twitter:image' ?\/>/";
            preg_match($regexPattern, $content, $match);
            $thumbnail = 'https'.Arr::get($match, 1);
        }

        return [
            'title' => $title,
            'excerpt' => $excerpt,
            'thumbnail' => $thumbnail
        ];
    }

    static private function _getBody($content){
        $regexPattern = "/<div class=\"(.{0,10}-post-content|entry-content)\".{0,100}>(.*?)<(nav|footer|div class=\"entry-meta|clear\")/";
        preg_match($regexPattern, $content, $match);
        $body = $match[0]??'';

        if(!$body){
            $regexPattern = "/<div itemprop=\"articleBody\".{0,100}>(.*?)<\/nav/";
            preg_match($regexPattern, $content, $match);
            $body = $match[1]??'';
        }

        //print_r($content);die();
        if(!$body){ //reportdays.com
            $regexPattern = "/<section class=\"rd-post-content\">(.*?)<\/section>/";
            preg_match($regexPattern, $content, $match);
            $body = $match[1]??'';
        }

        if(!$body){
            $regexPattern = "/<div class=\"entry.{0,20}\".{0,50}>(.*?)<!-- \.entry \/-->/";
            preg_match($regexPattern, $content, $match);
            $body = $match[1]??'';
        }


        if(!$body){ //kchivit.com
            $regexPattern = "/<div class=\"entry-content.{0,40}\".{0,50}>(.*?)<div class=\"post-share/";
            preg_match($regexPattern, $content, $match);
            $body = $match[1]??'';
        }
        //print_r($body);die();


        if(!$body){ //siamtimes.live
            $regexPattern = "/<div id='MyPostBody'>(.*?)<div class='clear'><\/div>/";
            preg_match($regexPattern, $content, $match);
            $body = $match[1]??'';
        }


        if(!$body){
            $regexPattern = "/<div class=\"tinymce\">(.*?)<div class=\"like_share\">/";
            preg_match($regexPattern, $content, $match);
            $body = $match[1]??'';
        }


        //siamtoptic
        if(!$body){
            $regexPattern = "/<div class=\"data_detail\".{0,50}>(.*?)<div class=\"ads_ViewBottom\"/";
            preg_match($regexPattern, $content, $match);
            $body = $match[1]??'';
        }

        if(!$body){
            $regexPattern = "/<article.{0,50}>(.*?)<\/article>/";
            preg_match($regexPattern, $content, $match);
            $body = $match[1]??'';
        }

        //print_r($body);die();
        //replace featured image
        $regexPattern = "/<div class=\"td-post-featured-image\">.*?<\/div>/";
        $body = preg_replace($regexPattern, '', $body);

        //print_r($body);die();
        return $body;
    }

    static  public function parseArticle($url, $type = 1){
        try {
            $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36';
            $client = new Client();
            $response = $client->get($url, ['headers' => ['User-Agent' => $userAgent]]);
            $content = $response->getBody()->getContents();

        }catch (BadResponseException $e){
            return [];
        }catch (Exception $e){
            return [];
        }

        $content = str_replace(["\t"], '', $content);
        $content = str_replace(["\n", '</p>'], self::SEPARATE, $content);

        $meta = self::_getMeta($content);
        $body = self::_getBody($content);

        //print_r($body);die();
        //remove script, style, ins
        $regexPattern = "/<style(.*?)<\/style>/";
        $body = preg_replace($regexPattern, ' ', $body);

        $regexPattern = "/<ins(.*?)<\/ins>/";
        $body = preg_replace($regexPattern, ' ', $body);

        $regexPattern = "/<(no)?script(.*?)<\/(no)?script>/";
        $body = preg_replace($regexPattern, ' ', $body);

        //mgid
        $regexPattern = "/<\!-- Composite Start -->(.*?)<\!-- Composite End -->/";
        $body = preg_replace($regexPattern, ' ', $body);

        //comment
        $regexPattern = "/<\!--.*?-->/";
        $body = preg_replace($regexPattern, ' ', $body);

        $regexPattern = "/<aside(.*?)<\/aside>/"; //taibann
        $body = preg_replace($regexPattern, ' ', $body);
        //print_r($body);die();
        $regexPattern = "/<div.{0,150}><\/div>/"; //taibann
        $body = preg_replace($regexPattern, ' ', $body);

        $ps = explode(self::SEPARATE, $body);
        //print_r($ps);die();

        foreach($ps as $i => $p){

            //remove link
            $regexPattern = "/<a.*? href=\".*?\".*?>.*?<\/a>/";
            $tmp = preg_replace($regexPattern, '', $p);
            if(strlen($tmp) < 3)
                unset($ps[$i]);

            $tmp = strip_tags($p, '<img><iframe><param>');
            if(strlen($tmp) < 3)
                unset($ps[$i]);


            //remove display none
            $regexPattern = "/<(p|div) style=\"display:none;\">.*?<\/p>/";
            $tmp = preg_replace($regexPattern, '', $p);

            if(strlen(strip_tags($tmp, '<img><iframe><param>')) < 3)
                unset($ps[$i]);
        }

        $ps = array_values($ps);
        //print_r($ps);die();
        $total = count($ps);

        $html = '';
        foreach($ps as $i => $p){

            if(self::_isEndArticles($p)) {
                break;
            }elseif(strpos($p, '.mp4') !== false || strpos($p, '.m3u8') !== false){
                $html .= self::_getMediaFile($p);

            }elseif(strpos($p, '<iframe') !== false){
                $html .= self::_getIframe($p);

            } elseif(strpos($p, '<img') !== false){
                $images = explode('<img', $p);
                foreach($images as $image){
                    $image = '<img'.$image;
                    $html .= self::_getImage($image, $meta['thumbnail']);
                }
            }elseif(strlen($p) > 8){
                $tmp = strip_tags($p);

                if($type == 1) //vietnam
                    if(str_word_count($tmp) < 4 && $i > $total - 4)
                        break;

                $b = false;
                if(strpos($p, '<h2>') !== false || strpos($p, '<h3>') !== false || strpos($p, '<h4>') !== false)
                    $b = true;

                $p = strip_tags($p, '<i><strong><b><em>');

                if(strlen($p) > 8){

                    if($b)
                        $p = '<b>'.$p.'</b>';
                    $html .= '<p class="text">'.$p.'</p>';
                }
            }
        }

        return array_merge(['content' => $html], $meta);
    }

}
