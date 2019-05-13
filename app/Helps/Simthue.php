<?php

namespace App\Helps;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use Illuminate\Support\Arr;

class Simthue
{

    const BASE_URL = 'http://api.pvaonline.net/request/';
    const API_KEY = 'mNsT6cy54mFheDeZRNQ3l4_sp';

    static public function createRequest(){
        $client = new Client();

        $data = [
            'key' => self::API_KEY,
            'service_id' => 29// yahoo
        ];
        $response = $client->get(self::BASE_URL.'create?'.http_build_query($data));
        $result = json_decode($response->getBody()->getContents(), true);
        return Arr::get($result, 'id');
    }

    static public function checkRequest($id){
        $client = new Client();
        $data = [
            'key' => self::API_KEY,
            'id' => $id
        ];
        $response = $client->get(self::BASE_URL.'check?'.http_build_query($data));
        $result = json_decode($response->getBody()->getContents(), true);
        $number = substr(Arr::get($result, 'number'), 2);

        $sms = Arr::first(Arr::get($result, 'sms', []));
        $tmp = explode('|', $sms);
        $contentSms = Arr::last($tmp);
        $regexPattern = "/\d\d\d\d\d/";
        preg_match($regexPattern, $contentSms, $match);
        $code = Arr::first($match);

        return ['number' => $number, 'code' => $code];
    }
}
