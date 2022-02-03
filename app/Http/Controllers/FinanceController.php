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

class FinanceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['role:fin|akc']);
    }

    
    public function excel_list_finance()
    {
        $uploads = FileUpload::whereIn('status', ['4', '5'])->get();
        return view('finance.excel-list', compact('uploads'));
    }

    public function payment_detail($id)
    {
        $uploads = FileUpload::where('id', $id)->first();
        $pay = Payment::where('file_id', $id)->first();

        /*
        $orders = Order::where([['file_id', '=' ,$id],['status', '1']])->get();
        $plan_arr = array();
        foreach ($orders as $order) {
            array_push($plan_arr,  $order->plan_type);
        }
        $plan_arr = array_count_values($plan_arr);
        $plans = Plan::all();
        */


        $tot_rec = 0;
        $orders = Order::where([['file_id', '=' ,$id],['status', '1']])->get();
        $tot_rec = count($orders);

        //all plans
        $plans = Plan::all();

        //prepare E-CARE plan group
        $plan_arr = array();
        $costing_arr = array();

        $tot_ecert = 0.00;
        $tot_pcr = 0.00;
        $tot_tpa = 0.00;
        

        foreach ($orders as $order) {

            if ($order->plan_type!=null && $order->plan_type!='' && $order->plan_type!='NO') {
                $date1 = date_create($order->dep_date);
                $date2 = date_create($order->return_date);
                $diff = date_diff($date1, $date2);

                $days = $diff->days;
                $price = Plan::where([['name', '=' ,$order->plan_type]])->pluck('price')->first();
                $perday = Plan::where([['name', '=' ,$order->plan_type]])->pluck('price_per_day')->first();
                $maxday = Plan::where([['name', '=' ,$order->plan_type]])->pluck('total_days')->first();

                //calculate for ECERT
                $cost = 0.00;   //plan price
                $addt = 0.00;   //additional day price
                $difday = 0;
                if ($price && $price > 0.00) {
                    $cost = $price;
                    if ($days && $maxday && ($days > $maxday)) {
                        $difday = ($maxday - $days);
                        $difday = ($difday>0?$difday:($difday*-1));
                        if ($perday) {
                            $addt = ($difday * $perday);
                            $cost = $cost + $addt;
                        }
                    }
                }

                //calculate for PCR
                $pcr = 0.00;   //pcr price



                //calculate for TPA
                $tpa = 0.00;   //tpa price



                //prepare costing array
                $tmpArr =  array (
                    'PLAN' => $order->plan_type,
                    'PRICE' => $price,
                    //'DEP' => $date1,
                    //'RTN' => $date2,
                    'DAYS' => $days,
                    'MAXDAY' => $maxday,
                    'DIFDAY' => $difday,
                    'PERDAY' => $perday,
                    'COST' => $cost,
                    'ADDT' => $addt,
                    'PCR' => $pcr,
                    'TPA' => $tpa,
                );
                array_push($costing_arr, $tmpArr); //prepare costing for each record
                array_push($plan_arr, $order->plan_type);   //grouping the selected plans
            }

            //echo $order;
            //array_push($plan_arr,  $order->plan_type);
        }
        // print_r($costing_arr);

        //group ECERT plan
        //$plan_arr = \Arr::where($plan_arr, function ($value, $key) {
        //    return $value!=null && $value!='' && $value!='NO';
        //});
        $plan_arr = array_count_values($plan_arr);  //count plan grouping
        //print_r($plan_arr);

        $invoice_arr = array();
        foreach ($plan_arr as $plan => $tot_count) {
            echo $plan .' === '. $tot_count ."<br>";
            $tot_cost = 0.00;
            foreach ($costing_arr as $cost) {
                if ($cost['PLAN'] == $plan) {
                    //echo $plan ." ==== ". $cost['COST'] ."<br>";
                    $tot_cost = $tot_cost + $cost['COST'];
                }
            }

            $tmpArr =  array (
                'PLAN' => $plan,
                'COUNT' => $tot_count,
                'COST' => $tot_cost,
            );
            array_push($invoice_arr, $tmpArr); //prepare costing for each record

            $tot_ecert = $tot_ecert + $tot_cost;
            //echo "TOT_COST=".$tot_cost."<br>";
            //echo "TOT_PLAN=".$tot_ecert."<br>";
        }
        //print_r($invoice_arr);
        //die();

        $tot_inv = $tot_ecert + $tot_pcr + $tot_tpa;



        $test = "Hello";

        return view('finance.payment', compact('uploads', 'pay', 'plan_arr', 'plans', 'invoice_arr', 'tot_inv', 'tot_rec', 'test'));
    }

    public function endorse_payment($id)
    {
        $uploads = FileUpload::where('id', $id)->first();
        $uploads->status = '5';

        $uploads->save();
        
        return redirect()->route('excel_list_finance');
    }
    
    public function download_payment($user_id, $file_id)
    {
        $payment = Payment::where('file_id', $file_id)->first();

        return Storage::download('/'.$user_id.'/payment/'.$file_id.'/'.$payment->pay_file);
    }
}
