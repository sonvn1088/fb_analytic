<?php

namespace App\Http\Controllers;

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
        print_r($result);
    }

}

