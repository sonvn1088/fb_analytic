<?php

namespace App\Http\Controllers;

use App\Helps\Simthue;
use App\Helps\Yahoo;
use App\Models\Account;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AccountController extends Controller
{

    public function generateAppToken(Request $request, $id){
        $account = Account::find($id);
        $client = new Client();

        $params = [
            'client_id' => $request->get('client_id'),
            'client_secret' => $request->get('client_secret'),
            'grant_type' => 'fb_exchange_token',
            'fb_exchange_token' => $request->get('fb_exchange_token'),
        ];
        try{
            $response = $client->get(config('facebook.exchange_token_url'), ['query' => $params]);
            $result =  \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
            $account->app_token = Arr::get($result, 'access_token');
            $account->status = Account::ACTIVE;
            $account->save();

            if($account->role['value'] == Account::EDITOR){ //editor
                $account->setPagesToken(false);
            }

        }catch (BadResponseException $e){
            $result = ['error' => ['message' => $e->getMessage(), 'code' => $e->getCode()]];
        }catch(Exception $e){
            $result = ['error' => ['message' => $e->getMessage(), 'code' => $e->getCode()]];
        }
    }

    public function getInfo($id){
        $account = Account::find($id);
        return $account->only(['first_name', 'last_name', 'email', 'email_password', 'birthday', 'gender']);
    }

    public function checkSms($id){
        return Simthue::checkRequest($id);
    }

    public function createYahooAccount($id){
        $account = Account::find($id);
        $data = $account->only(['first_name', 'last_name', 'email', 'email_password', 'birthday', 'gender']);
        $data['request_id'] = Simthue::createRequest();
        $url = 'https://login.yahoo.com/account/create?intl=vn&data='.base64_encode(json_encode($data));
        return redirect()->intended($url);
    }
}

