<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FileUpload;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function send_mail ($subject_to_send, $to_name, $to_email, $body, $title)
    {
        try {
            $data = array('name' => $to_name, "body" => $body);
            Mail::send('email.mail', $data, function($message) use ($to_name, $to_email, $subject_to_send, $title) {
                $message->to($to_email, $to_name)->subject($subject_to_send);
                $message->from('a.khairicare@gmail.com', $title);
            });
        } catch (\Exception $ex) {
            //dd($ex);
        } 
    }
}
