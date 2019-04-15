<?php

namespace App\Helps;



use Illuminate\Support\Facades\Mail;

class General
{
    static public function sendMail($account){
        Mail::send('emails.reminder', ['account' => $account], function ($mail) use ($account) {
            $subject = 'Your account #'.$account->first_name . ' ' . $account->last_name . ' was disabled';
            $mail->from('hello@mfbac.com', 'MFBAC')
                ->to('sonvn1088@gmail.com', 'Son Nguyen')
                ->subject($subject);
        });

    }


}
