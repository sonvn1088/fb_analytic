<?php

namespace App\Helps;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\TransferStats;

class Yahoo
{
    const YAHOO_LOGIN_URL = 'https://login.yahoo.com/';
    const BROWSER_INFO = '{"language":"en-US","color_depth":24,"device_memory":8,"hardware_concurrency":4,"resolution":{"w":1366,"h":768},"available_resolution":{"w":1366,"h":738},"timezone_offset":-420,"session_storage":1,"local_storage":1,"indexed_db":1,"open_database":1,"cpu_class":"unknown","navigator_platform":"Win32","canvas":"canvas winding:yes~canvas","webgl":1,"webgl_vendor":"Google Inc.~ANGLE (Intel(R) HD Graphics 4000 Direct3D11 vs_5_0 ps_5_0)","adblock":0,"has_lied_languages":0,"has_lied_resolution":0,"has_lied_os":0,"has_lied_browser":0,"touch_support":{"points":0,"event":0,"start":0},"audio_fp":"124.0434474653739","plugins":{"count":3,"hash":"e43a8bc708fc490225cde0663b28278c"},"fonts":{"count":49,"hash":"411659924ff38420049ac402a30466bc"}}';
    const USER_AGENT = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36';


    static public function register($info){
        /*$ch = self::initCurl();
        curl_setopt($ch, CURLOPT_URL, self::YAHOO_LOGIN_URL.'account/create');
        $content = curl_exec($ch);
        print_r($content);die();*/

        $limit = 2;
        $requestId = 'RSGHi6_Y4nV7';//Simthue::createRequest();

        $cookieFile = Storage::path('cookie_jar.txt');
        $cookieJar = new FileCookieJar($cookieFile, TRUE);
        $client = new Client(['cookies'  => $cookieJar]);

        $response = $client->get(self::YAHOO_LOGIN_URL.'account/create', ['headers' => ['User-Agent' => self::USER_AGENT ]]);
        sleep(5);

        $phone = null;
        $i = 0;
        while(!$phone && $i++ < $limit){
            $smsResult = Simthue::checkRequest($requestId);
            $phone = Arr::get($smsResult, 'number');
            sleep(5);
        }

        $info['phone'] = $phone;

        $result = self::_submitInfo($client, $info, $response);
        $result = self::_verifyPhone($client, $info['phone'], Arr::get($result, 'response'), Arr::get($result, 'next_url'));

        sleep(5);
        $code = null;
        $i = 0;
        while(!$code && $i++ < $limit){
            $smsResult = Simthue::checkRequest($requestId);
            $code = Arr::get($smsResult, 'code');
            sleep(5);
        }

        $respone = self::_submitCode($client, $code, Arr::get($result, 'response'), Arr::get($result, 'next_url'));

        return $respone;
    }

    static private function _submitInfo($client, $info, $response){
        $content = $response->getBody()->getContents();
        //print_r($content);die();
        $render = round(microtime(true) * 1000) + 400;
        $crumb = self::_getHiddenValue('crumb', $content);
        $acrumb = self::_getHiddenValue('acrumb', $content);
        $sessionIndex = self::_getHiddenValue('sessionIndex', $content);
        $tos0 = self::_getHiddenValue('tos0', $content);
        $done = self::_getHiddenValue('done', $content);

        $regexPattern = "/<form id=\"regform\" action=\"(.*?)\"/";
        preg_match($regexPattern, $content, $match);
        $postUrl = $match[1];

        sleep(4);
        $ts['serve'] =  round(microtime(true) * 1000);
        $ts['render'] =  $render;


        $params = [
            'browser-fp-data' => json_encode(array_merge(json_decode(self::BROWSER_INFO, true), ['ts' => $ts])),
            'specId' => 'yidReg',
            'cacheStored' => 'true',
            'crumb' => $crumb,
            'acrumb' => $acrumb,
            'sessionIndex' => $sessionIndex,
            'done' => $done,
            'googleIdToken' => '',
            'authCode' => '',
            'attrSetIndex' => 0,
            'tos0' => $tos0,
        ];


        $params = array_merge($params, $info);


        $options = [
            'form_params' => $params,
            'headers' => ['User-Agent' => self::USER_AGENT],
            'allow_redirects' => true,
            'on_stats' => function (TransferStats $stats) use (&$url) {
                $url = $stats->getEffectiveUri()->__toString();
            }
        ];

        $response = $client->post($postUrl, $options);
        return ['response' => $response, 'next_url' => $url];
    }

    static private function _verifyPhone($client, $phone, $response, $nextUrl){
        $content = $response->getBody()->getContents();
        print_r($nextUrl);
        print_r($content);die();
        $render = round(microtime(true) * 1000);
        $crumb = self::_getHiddenValue('crumb', $content);
        $acrumb = self::_getHiddenValue('acrumb', $content);
        $sessionIndex = self::_getHiddenValue('sessionIndex', $content);
        $locale = self::_getHiddenValue('locale', $content);

        $regexPattern = "/<button type=\"submit\" name=\"sendCode\" class=\".{0,100}\" value=\"(.*?)\"/";
        preg_match($regexPattern, $content, $match);
        $sendCode = Arr::get($match, 1);

        sleep(5);
        $ts['serve'] =  round(microtime(true) * 1000);
        $ts['render'] =  $render;

        $params = [
            'browser-fp-data' => json_encode(array_merge(json_decode(self::BROWSER_INFO, true), ['ts' => $ts])),
            'crumb' => $crumb,
            'acrumb' => $acrumb,
            'sessionIndex' => $sessionIndex,
            'displayName' => '',
            'context' => 'REGISTRATION',
            'locale' => $locale,
            'thirdPartyAuthProvider' => '',
            'formattedPhone' => $phone,
            'shortCountryCode' => 'VN',
            'editedPhoneNumber' => (int)$phone,
            'sendCode' => $sendCode,
        ];

        $options = [
            'form_params' => $params,
            'headers' => ['User-Agent' => self::USER_AGENT],
            'allow_redirects' => true,
            'on_stats' => function (TransferStats $stats) use (&$url) {
                $url = $stats->getEffectiveUri()->__toString();
            }
        ];

        $response = $client->post($nextUrl, $options);
        return ['response' => $response, 'next_url' => $url];
    }

    static private function _submitCode($ch, $code, $response, $nextUrl){
        $content = $response->getBody()->getContents();
        print_r($content);die();
        $render = round(microtime(true) * 1000);
        $crumb = self::_getHiddenValue('crumb', $content);
        $acrumb = self::_getHiddenValue('acrumb', $content);
        $sessionIndex = self::_getHiddenValue('sessionIndex', $content);
        $referenceId = self::_getHiddenValue('referenceId', $content);
        $bearer = self::_getHiddenValue('bearer', $content);
        $codeDigits = self::_getHiddenValue('codeDigits', $content);
        $editedPhoneNumber = self::_getHiddenValue('editedPhoneNumber', $content);
        $numberAttemptsRemaining = self::_getHiddenValue('numberAttemptsRemaining', $content);
        $lastSentTime = self::_getHiddenValue('lastSentTime', $content);
        $locale = self::_getHiddenValue('locale', $content);

        sleep(5);
        $ts['serve'] =  round(microtime(true) * 1000);
        $ts['render'] =  $render;

        $params = [
            'browser-fp-data' => json_encode(array_merge(json_decode(self::BROWSER_INFO, true), ['ts' => $ts])),
            'crumb' => $crumb,
            'acrumb' => $acrumb,
            'sessionIndex' => $sessionIndex,
            'displayName' => '',
            'referenceId' => $referenceId,
            'context' => 'REGISTRATION',
            'bearer' => $bearer,
            'codeDigits' => $codeDigits,
            'locale' => $locale,
            'editedPhoneNumber' => $editedPhoneNumber,
            'numberAttemptsRemaining' => $numberAttemptsRemaining,
            'lastSentTime' => $lastSentTime,
            'code' => $code,
            'verifyCode' => 'true'
        ];

        $options = [
            'form_params' => $params,
            'headers' => ['User-Agent' => self::USER_AGENT ],
            'allow_redirects' => true,
        ];

        $response = $client->post($nextUrl, $options);
        return $response;

    }
    
    static public function checkEmail($email){
        $cookieFile = 'cookie_jar.txt';
        $cookieJar = new FileCookieJar($cookieFile, TRUE);
        $client = new Client(['cookies' => $cookieJar]);

        $response = $client->get(config('yahoo.login_url'));
        $content =  $response->getBody()->getContents();

        $acrumb = self::_getHiddenValue('acrumb', $content);
        $sessionIndex = self::_getHiddenValue('sessionIndex', $content);

        $params = [
            'acrumb' => $acrumb,
            'sessionIndex' => $sessionIndex,
            'username' => $email,
            'passwd' => '',
            'signin' => 'Next',
            'persistent' => 'y'
        ];


        $response = $client->post(config('yahoo.login_url'), ['form_params' => $params]);
        $content =  $response->getBody()->getContents();

        if(strpos($content, 'ERROR_INVALID_USERNAME'))
            return true;
    }

    static private function _getHiddenValue($name, $content){
        $regexPattern = "/<input type=\"hidden\"( value=\"(.*?)\")? name=\"$name\"( value=\"(.*?)\")?/";
        preg_match($regexPattern, $content, $match);
        return isset($match[4])?Arr::get($match, 4):Arr::get($match, 2);
    }

    static function initCurl(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, Storage::path('cookie.txt'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, Storage::path('cookie.txt'));
        curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
        return $ch;
    }
}
