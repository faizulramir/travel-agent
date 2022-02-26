<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FileUpload;
use App\Models\Order;
use App\Models\Payment;
use App\Models\EcertCnt;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\DashboardUser;

class RegenJsonController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['role:akc']);
    }

    
    public function regen(Request $request)
    {
        $filess = FileUpload::all();
        
        foreach ($filess as $key => $files) {
            $tot_rec = 0;
            $orders = Order::where([['file_id', '=' ,$files->id],['status', '1']])->get();
            $tot_rec = count($orders);

            //all plans
            $plans = Plan::all();

            //prepare E-CARE plan group
            $plan_arr = array();
            $tpa_arr = array();
            $costing_arr = array();
            $pcr_arr = array();

            $tot_ecert = 0.00;
            $tot_pcr = 0.00;
            $tot_tpa = 0.00;

            $pcr_cnt = 0;
            $tpa_cnt = 0;
            $price_pcr = Plan::where([['name', '=' , 'pcr']])->pluck('price')->first();

            foreach ($orders as $order) {
                if ($order->plan_type!=null && $order->plan_type!='' && $order->plan_type != 'NO') {
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

                    //prepare costing array
                    $tmpArr =  array(
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
                        'PCR' => null,
                        'TPA' => null,
                        'TPANAME' => null,
                    );
                    array_push($costing_arr, $tmpArr); //prepare costing for each record
                    array_push($plan_arr, $order->plan_type);   //grouping the selected plans
                }

                if ($order->pcr != null && $order->pcr != '' && $order->pcr == 'PCR') {
                    //calculate for PCR
                    $pcr = 0.00;   //pcr price
                    $pcr_name = 'PCR';
                    if ($order->pcr == 'PCR' || $order->pcr == 'YES') {
                        $pcr = $pcr + $price_pcr;
                        $pcr_cnt = $pcr_cnt + 1;
                        array_push($pcr_arr, $pcr_name);
                    }
                }

                if ($order->tpa != null && $order->tpa !='' && $order->tpa != 'NO') {
                    //calculate for TPA
                    $tpa_name = null;
                    $tpa_price = 0.00;
                    $tpa_name = $order->tpa;
                    $plan_tpa_price = Plan::where([['name', '=' ,$tpa_name]])->pluck('price')->first();
                    if ($plan_tpa_price && $plan_tpa_price > 0.00) {
                        $tpa_price = $plan_tpa_price;
                    }

                    array_push($tpa_arr, $tpa_name);   //grouping the selected plans

                    //prepare costing array
                    $tmpArr =  array(
                        'PLAN' => null,
                        'PRICE' => null,
                        //'DEP' => $date1,
                        //'RTN' => $date2,
                        'DAYS' => null,
                        'MAXDAY' => null,
                        'DIFDAY' => null,
                        'PERDAY' => null,
                        'COST' => null,
                        'ADDT' => null,
                        'PCR' => null,
                        'TPA' => $tpa_price,
                        'TPANAME' => $order->tpa,
                    );
                    array_push($costing_arr, $tmpArr); //prepare costing for each record
                }
            }
            
            $plan_arr = array_count_values($plan_arr);  //count plan grouping
            $tpa_arr = array_count_values($tpa_arr);  //count tpa grouping
            $pcr_arr = array_count_values($pcr_arr);  //count pcr grouping
            
            // $tot_pcr = (isset($pcr_arr['PCR']) ? $pcr_arr['PCR'] : 0) * $price_pcr;
            // $pcr_detail = new \stdClass();
            // $pcr_detail->name = 'PCR';
            // $pcr_detail->price = $tot_pcr;
            // $pcr_detail->cnt = ;

            $pcrArr =  array(
                'PLAN' => 'PCR',
                'COUNT' => (isset($pcr_arr['PCR']) ? $pcr_arr['PCR'] : 0),
                'PRICE' => Plan::where([['name', '=' ,'pcr']])->pluck('price')->first(),
                'COST' => (Plan::where([['name', '=' ,'pcr']])->pluck('price')->first()) * (isset($pcr_arr['PCR']) ? $pcr_arr['PCR'] : 0),
            );

            $invoice_arr = array();
            $tpa_pcr_arr = array();
            foreach ($plan_arr as $plan => $tot_count) {
                $tot_cost = 0.00;
                foreach ($costing_arr as $cost) {
                    if ($cost['PLAN'] == $plan) {
                        $tot_cost = $tot_cost + $cost['COST'];
                    }
                }

                $tmpArr =  array(
                    'PLAN' => $plan,
                    'PRICE' => Plan::where([['name', '=' ,$plan]])->pluck('price')->first(),
                    'PCR_COUNT' => $pcr_cnt,
                    'PCR_TOT' => $tot_pcr,
                    'COUNT' => $tot_count,
                    'COST' => $tot_cost,
                );
                array_push($invoice_arr, $tmpArr); //prepare costing for each record

                $tot_ecert = $tot_ecert + $tot_cost;
            }
            // dd($invoice_arr);
            $tpa_total_arr = array();
            foreach ($tpa_arr as $tpa => $tot_count) {
                $tpa_cost = 0.00;
                foreach ($costing_arr as $cost) {
                    if ($cost['TPANAME'] == $tpa) {
                        $tpa_cost = $tpa_cost + $cost['TPA'];
                    }
                }

                $tmpArr =  array(
                    'PLAN' => $tpa,
                    'PRICE' => Plan::where([['name', '=' ,$tpa]])->pluck('price')->first(),
                    'COUNT' => $tot_count,
                    'COST' => $tpa_cost,
                );
                array_push($tpa_pcr_arr, $tmpArr); //prepare costing for each record
                $tot_tpa = $tot_tpa + $tpa_cost;
            }
            // $tot_inv = $tot_ecert + $pcrArr['COST'] + $tot_tpa;

            $dt = Carbon::now();
            $date_today = $dt->toDateString();

            $invoice_num = null;
            if ($orders && $orders[0]) {
                $invoice_num = $orders[0]->invoice;
            }

            if ($pcrArr['COST'] == 0 || $pcrArr['COST'] == '0') {
            } else {
                array_push($tpa_pcr_arr, $pcrArr);
            }
            $disArr = null;
            if ($files->status == '2.1') {
                $disArr = array(
                    'PLAN' => 'DISCOUNT',
                    'PRICE' => $request->discount,
                    'COUNT' => '1',
                    'COST' => $request->discount,
                );
            }
            else {
                if ($files->discount && $files->discount !== '0') {
                    $disArr = array(
                        'PLAN' => 'DISCOUNT',
                        'PRICE' => $files->discount,
                        'COUNT' => '1',
                        'COST' => $files->discount,
                    );
                }
            }

            $tot_inv = $tot_ecert;
            //discard comma in value --
            if ($disArr && $disArr['COST']) {
                $tot_inv = $tot_ecert - $disArr['COST'];
                
                //$discount = str_replace(',', '', ''.$disArr['COST']);
                //$tot_inv = $tot_ecert - $discount;
            }

            $tot_inv2 = $tot_inv + ($pcrArr && $pcrArr['COST']? $pcrArr['COST'] : 0) + $tot_tpa;
            
            $arr_data = array(
                'tpa_pcr_arr' => $tpa_pcr_arr,
                'files' => $files,
                'invoice_arr' => $invoice_arr,
                'tot_inv' => $tot_inv,
                'tot_inv2' => $tot_inv2,
                'disArr' => $disArr,
                'tot_rec' => $tot_rec,
                'tpa_arr' => $tpa_arr,
                'tpa_total_arr' => $tpa_total_arr,
                'date_today' => $date_today,
                'invoice_num' => $invoice_num,
            );
            // dd(json_encode($arr_data));

            $files->json_inv = json_encode($arr_data);
            $files->save();
        }
    }
}
