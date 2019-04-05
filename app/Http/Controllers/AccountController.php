<?php

namespace App\Http\Controllers;

use App\Helps\Facebook;
use App\Helps\Yahoo;
use App\Models\Account;

class AccountController extends Controller
{

    public function generateToken($id){
        $account = Account::find($id);
        $data = Facebook::generateToken($account->fb_id?:$account->username, $account->password);
        print_r($data);
        if(!isset($data['access_token'])){
            die();
        }
        $account->token = $data['access_token'];
        $account->login_info = json_encode($data);
        $account->save();
    }

    public function updateAccount($id){
        $account = Account::find($id);
        $user = Facebook::getUser('me', $account->token);
        $account->email = $user['email'];
        $account->first_name = $user['first_name'];
        $account->last_name = $user['last_name'];
        $account->middle_name = $user['middle_name']??null;
        $account->friends = $user['friends']['summary']['total_count'];
        $account->fb_id = $user['id'];
        $tmp = explode('/', $user['birthday']);
        $account->birthday = $tmp[2].'-'.$tmp[0].'-'.$tmp[1];
        $account->country = $user['location']['location']['country']??null;
        if(isset($user['gender']))
            $account->gender = $user['gender'] == 'male'?2:1;
        $account->save();
    }


    public function scanAccounts(){
        return Links::getTopLinks();
    }

    public function importAccounts(){
        $accounts = [];
        foreach($accounts as $row){
            $tmpName = explode(' ', $row[1]);
            $tmpBirthday = explode('/', $row[2]);
            $data = [
                'email' => strpos($row[0], '@')?$row[0]:$row[0].'@gmail.com',
                'first_name' => current($tmpName),
                'last_name' => end($tmpName),
                'birthday' => $tmpBirthday[2].'-'.$tmpBirthday[0].'-'.$tmpBirthday[1],
                'username' => strpos($row[3], '000')?null:$row[3],
                'fb_id' => strpos($row[3], '000')?$row[3]:null,
                'password' => $row[4],
            ];

            $account = Account::firstOrNew($data);
            $account->save();
        }
    }

}

