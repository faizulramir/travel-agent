<?php
    
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Session;
use Stripe;
use App\Models\DashboardUser;
use App\Models\FileUpload;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Arr;
class StripeController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe($pay_id, $pay_total, $pay_name)
    {
        //dd($pay_id, $pay_total, $pay_name);

        if ($pay_name == 'cc') {
            return view('stripe.cc', compact('pay_total', 'pay_id'));
        } 
        else if ($pay_name == 'fpx') {
            $str_1 = str_replace("RM", "", $pay_total);
            $str_2 = str_replace(",", "", $str_1);
            $str_3 = str_replace(" ", "", $str_2);
            $str_4 = str_replace(".00", "", $str_3);
            $stripe = new \Stripe\StripeClient('sk_test_51KPICVGHIWVASdQSz2rhGCGTJP00uuxWBynz5PQr1jF4RxVI2rXZp5kzw1KXClhW5QGMZf2IZiR8L2pgbYuIvL2F00UCQl6ZiV');

            $intent = $stripe->paymentIntents->create(
                ['payment_method_types' => ['fpx'], 'amount' => $str_4 * 100, 'currency' => 'myr']
            );
            $clientSecret = Arr::get($intent, 'client_secret');
            
            return view('stripe.fpx', compact('pay_total', 'pay_id', 'clientSecret'));
        }
    }
   
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request)
    {
        $str_1 = str_replace("RM", "", $request->pay_total);
        $str_2 = str_replace(",", "", $str_1);
        $str_3 = str_replace(" ", "", $str_2);
        $str_4 = str_replace(".00", "", $str_3);

        if ($request->pay_name == 'cc') {
            Stripe\Stripe::setApiKey('sk_test_51KPICVGHIWVASdQSz2rhGCGTJP00uuxWBynz5PQr1jF4RxVI2rXZp5kzw1KXClhW5QGMZf2IZiR8L2pgbYuIvL2F00UCQl6ZiV');
            Stripe\Charge::create ([
                    "amount" => $str_4 * 100,
                    "currency" => "myr",
                    "source" => $request->stripeToken,
                    "description" => "This payment is tested purpose"
            ]);
        } else if ($request->pay_name == 'fpx') {
            
        } 

        // 4242424242424242
        // 123
        // dd($request->all());
        $dt = Carbon::now();

        $payment =  new Payment;
        $payment->pay_date = $dt->toDateString();
        $payment->pay_by = $request->pay_name;
        $payment->pay_file = $request->pay_name;
        $payment->pay_total = $request->pay_total;
        $payment->file_id = $request->pay_id;
        $payment->save();

        $upload = FileUpload::where('id', $request->pay_id)->first();
        $upload->status = '4';
        $upload->save();
   
        Session::flash('success', 'Payment successful!');
        
        // $request->pay_id;
        if (auth()->user()->hasAnyRole('tra')) {
            return redirect()->route('excel_list');
        } else if (auth()->user()->hasAnyRole('akc')) {
            return redirect()->route('excel_list_admin');
        } else if (auth()->user()->hasAnyRole('ind')) {
            return redirect()->route('application_list');
        } else if (auth()->user()->hasAnyRole('ag')) {
            return redirect()->route('excel_list_agent');
        } 
    }
}