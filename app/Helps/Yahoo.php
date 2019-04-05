<?php

namespace App\Helps;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;

class Yahoo
{

    function register($info){
        $ch = initCurl();
        curl_setopt($ch, CURLOPT_URL, YAHOO_LOGIN_URL.'account/create');
        $content = curl_exec($ch);

        $content = verifyPhone($ch, $info, $content);
        $content = verifyPhone($ch, $info['phone'], $content);
        $code = '34242';
        submitCode($ch, $code, $content);

    }

    static private function _submitInfo($ch, $info, $content){
        $render = round(microtime(true) * 1000);
        $crumb = self::_getHiddenValue('crumb', $content);
        $acrumb = self::_getHiddenValue('acrumb', $content);
        $sessionIndex = self::_getHiddenValue('sessionIndex', $content);
        $tos0 = self::_getHiddenValue('tos0', $content);

        $regexPattern = "/<form id=\"regform\" action=\"(.*?)\"/";
        preg_match($regexPattern, $content, $match);
        $postUrl = $match[1];

        $ts['serve'] =  round(microtime(true) * 1000);
        sleep(5);
        $ts['render'] =  $render;


        $params = [
            'browser-fp-data' => json_encode(array_merge(json_decode(BROWSER_INFO, true), ['ts' => $ts])),
            'specId' => 'yidReg',
            'cacheStored' => 'true',
            'crumb' => $crumb,
            'acrumb' => $acrumb,
            'sessionIndex' => $sessionIndex,
            'done' => 'https://www.yahoo.com',
            'googleIdToken' => '',
            'authCode' => '',
            'attrSetIndex' => 0,
            'tos0' => $tos0,
        ];


        $params = array_merge($params, $info);

        curl_setopt($ch, CURLOPT_URL, $postUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  http_build_query($params));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $content = curl_exec($ch);
        return $content;
    }

    static private function _verifyPhone($ch, $phone, $content){
        $render = round(microtime(true) * 1000);
        $crumb = self::_getHiddenValue('crumb', $content);
        $acrumb = self::_getHiddenValue('acrumb', $content);
        $sessionIndex = self::_getHiddenValue('sessionIndex', $content);
        $locale = self::_getHiddenValue('locale', $content);

        $regexPattern = "/<button type=\"submit\" name=\"sendCode\" class=\".{0,100}\" value=\"(.*?)\"/";
        preg_match($regexPattern, $content, $match);
        $sendCode = $match[1];

        $ts['serve'] =  round(microtime(true) * 1000);
        sleep(5);
        $ts['render'] =  $render;

        $params = [
            'browser-fp-data' => json_encode(array_merge(json_decode(BROWSER_INFO, true), ['ts' => $ts])),
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

        $verifyUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_setopt($ch, CURLOPT_URL, $verifyUrl);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  http_build_query($params));
        $content = curl_exec($ch);
        return $content;
    }

    static private function _submitCode($ch, $code, $content){
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

        $ts['serve'] =  round(microtime(true) * 1000);
        sleep(5);
        $ts['render'] =  $render;

        $params = [
            'browser-fp-data' => json_encode(array_merge(json_decode(BROWSER_INFO, true), ['ts' => $ts])),
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

        $submitUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_setopt($ch, CURLOPT_URL, $submitUrl);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  http_build_query($params));
        $content = curl_exec($ch);

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

    private function _getHiddenValue($name, $content){
        $regexPattern = "/<input type=\"hidden\"( value=\"(.*?)\")? name=\"$name\"( value=\"(.*?)\")?/";
        preg_match($regexPattern, $content, $match);
        return isset($match[4])?$match[4]:$match[2];
    }
}
