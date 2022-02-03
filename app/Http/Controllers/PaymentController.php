<?php

namespace App\Http\Controllers;

use App\Models\DashboardUser;
use App\Models\FileUpload;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use PDF;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['role:tra|akc|ag|ind']);
    }

    public function payment_old($id)
    {
        $tot_rec = 0;
        //all orders
        $orders = Order::where([['file_id', '=' ,$id],['status', '1']])->get();
        $tot_rec = count($orders);
        //all plans
        $plans = Plan::all();

        //prepare E-CARE plan group
        $plan_arr = array();
        $pcr_arr = array();
        $tpa_arr = array();
        $costing_arr = array();

        $tot_ecert = 0.00;
        $tot_pcr = 0.00;
        $tot_tpa = 0.00;
        
        foreach ($orders as $order) {

            //calculate for ECERT
            $cost = 0.00;   //plan price
            $addt = 0.00;   //additional day price
            $difday = 0;
            $price = 0.00;   //
            $perday = 0.00;   //
            $maxday = 0;   //
            $days = 0;
            if ($order->plan_type!=null && $order->plan_type!='' && $order->plan_type!='NO') {
                $date1 = date_create($order->dep_date);
                $date2 = date_create($order->return_date);
                $diff = date_diff($date1, $date2);

                $days = $diff->days;
                $price = Plan::where([['name', '=' ,$order->plan_type]])->pluck('price')->first();
                $perday = Plan::where([['name', '=' ,$order->plan_type]])->pluck('price_per_day')->first();
                $maxday = Plan::where([['name', '=' ,$order->plan_type]])->pluck('total_days')->first();

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

                array_push($plan_arr, $order->plan_type);   //grouping the selected plans
            }

            //calculate for PCR
            $pcr = '';
            $pcr_cost = 0.00;   //pcr price
            if ($order->pcr!=null && $order->pcr!='' && $order->pcr!='NO') {
                $pcr = 'PCR';
                $pcr_cost = Plan::where([['name', '=' ,$pcr]])->pluck('price')->first();

                array_push($pcr_arr, $pcr);   //grouping the selected plans
            }

            //calculate for TPA
            $tpa = '';
            $tpa_cost = 0.00;   //tpa price
            if ($order->tpa!=null && $order->tpa!='' && $order->tpa!='NO') {
                $tpa = $order->tpa;
                $tpa_cost = Plan::where([['name', '=' ,]])->pluck('price')->first();

                array_push($tpa_arr, $tpa);   //grouping the selected plans
            }

            //print_r($plan_arr);


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
                'PCR_COST' => $pcr_cost,
                'TPA' => $tpa,
                'TPA_COST' => $tpa_cost,
            );
            array_push($costing_arr, $tmpArr); //prepare costing for each record
            //array_push($plan_arr, $order->plan_type);   //grouping the selected plans

            //echo $order;
            //array_push($plan_arr,  $order->plan_type);
        }
        //print_r($costing_arr);

        //group ECERT plan
        //$plan_arr = \Arr::where($plan_arr, function ($value, $key) {
        //    return $value!=null && $value!='' && $value!='NO';
        //});
        $plan_arr = array_count_values($plan_arr);  //count plan grouping
        $pcr_arr = array_count_values($pcr_arr);  //count plan grouping
        $tpa_arr = array_count_values($tpa_arr);  //count plan grouping
        //print_r($plan_arr);

        $invoice_arr = array();
        foreach ($plan_arr as $plan => $tot_count) {
            //echo $plan .' === '. $tot_count ."<br>";
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

        foreach ($pcr_arr as $plan => $tot_count) {
            echo $plan .' === '. $tot_count ."<br>";
            $tot_pcr = 0.00;
            foreach ($costing_arr as $cost) {
                if ($cost['PCR'] == $plan) {
                    //echo $plan ." ==== ". $cost['COST'] ."<br>";
                    $tot_pcr = $tot_pcr + $cost['PCR_COST'];
                }
            }

            $tmpArr =  array (
                'PLAN' => $plan,
                'COUNT' => $tot_count,
                'PCR_COST' => $tot_pcr,
            );
            array_push($invoice_arr, $tmpArr); //prepare costing for each record

            $tot_ecert = $tot_ecert + $tot_cost;
            //echo "TOT_COST=".$tot_cost."<br>";
            //echo "TOT_PLAN=".$tot_ecert."<br>";
        }

        foreach ($tpa_arr as $plan => $tot_count) {
            echo $plan .' === '. $tot_count ."<br>";
        }








        $tot_inv = $tot_ecert + $tot_pcr + $tot_tpa;

        die();
        return view('payment.payment', compact('plan_arr',  'plans', 'id', 'invoice_arr', 'tot_inv', 'tot_rec', 'pcr_arr', 'tpa_arr'));
    }
    
    public function payment($id)
    {
        $tot_rec = 0;
        //all orders
        $orders = Order::where([['file_id', '=' ,$id],['status', '1']])->get();
        $tot_rec = count($orders);
        //all plans
        $plans = Plan::all();

        //prepare E-CARE plan group
        $plan_arr = array();
        $pcr_arr = array();
        $tpa_arr = array();
        $costing_arr = array();

        $tot_ecert = 0.00;
        $tot_pcr = 0.00;
        $tot_tpa = 0.00;
        
        foreach ($orders as $order) {

            //calculate for ECERT
            $cert_name = null;
            $cert_price = 0.00;
            $addt_price = 0.00;
            $addt_day = 0;

            $plan_perday = 0.00;   //
            $plan_maxday = 0;   //

            if ($order->plan_type!=null && $order->plan_type!='' && $order->plan_type!='NO') {
                $cert_name = $order->plan_type;
                $dep_date = date_create($order->dep_date);
                $rtn_date = date_create($order->return_date);
                $diff = date_diff($dep_date, $rtn_date);
                $diff_days = $diff->days;
                $plan_price = Plan::where([['name', '=' ,$order->plan_type]])->pluck('price')->first();
                $plan_maxday = Plan::where([['name', '=' ,$order->plan_type]])->pluck('total_days')->first();
                $plan_perday = Plan::where([['name', '=' ,$order->plan_type]])->pluck('price_per_day')->first();

                if ($plan_price && $plan_price > 0.00) {
                    $cert_price = $plan_price;
                    if ($diff_days && $plan_maxday && ($diff_days > $plan_maxday)) {
                        $addt_day = ($plan_maxday - $diff_days);
                        $addt_day = ($addt_day>0?$addt_day:($addt_day*-1));
                        if ($plan_perday) {
                            $addt_price = ($addt_day * $plan_perday);
                            //$cost = $cost + $addt_price;
                        }
                    }
                }
                array_push($plan_arr, $cert_name);   //grouping the selected plans
            }



            //calculate for PCR
            $pcr_name = null;
            $pcr_price = 0.00;
            if ($order->pcr!=null && $order->pcr!='' && $order->pcr!='NO') {
                $pcr_name = 'PCR';
                $plan_price = Plan::where([['name', '=' ,$pcr_name]])->pluck('price')->first();
                if ($plan_price && $plan_price > 0.00) {
                    $pcr_price = $plan_price;
                }

                echo "(".$pcr_name.")==(".$pcr_price.")<br>";
                array_push($plan_arr, $pcr_name);   //grouping the selected plans
            }

            //calculate for TPA
            $tpa_name = null;
            $tpa_price = 0.00;
            if ($order->tpa!=null && $order->tpa!='' && $order->tpa!='NO') {
                $tpa_name = $order->tpa;
                $plan_price = Plan::where([['name', '=' ,$tpa_name]])->pluck('price')->first();
                if ($plan_price && $plan_price > 0.00) {
                    $tpa_price = $plan_price;
                }

                echo "(".$tpa_name.")==(".$tpa_price.")<br>";
                array_push($plan_arr, $tpa_name);   //grouping the selected plans
            } 

            //prepare costing array
            $tmpArr =  array (
                'PLAN' => $cert_name,
                'PRICE' => $cert_price,
                //'DEP' => $date1,
                //'RTN' => $date2,
                'DAYS' => $addt_day,
                //'MAXDAY' => $plan_maxday,
                'PERDAY' => $plan_perday,
                'ADDT' => $addt_price,

                'PCR' => $pcr_name,
                'PCR_PRICE' => $pcr_price,
                'TPA' => $tpa_name,
                'TPA_PRICE' => $tpa_price,
            );
            array_push($costing_arr, $tmpArr); //prepare costing for each record
        }
        echo "<br><br>COSTING====<br>";
        print_r($costing_arr);
        echo "<br><br>PLAN====<br>";
        $plan_arr = array_count_values($plan_arr);  //count plan grouping
        print_r($plan_arr);


        $invoice_arr = array();
        foreach ($plan_arr as $plan => $count) {
            //echo $plan .' === '. $tot_count ."<br>";
            $tot_cost = 0.00;
            foreach ($costing_arr as $cost) {
                if ($cost['PLAN'] == $plan) {
                    //echo $plan ." ==== ". $cost['PRICE'] ."<br>";
                    $tot_cost = $tot_cost + $cost['PRICE'];
                }
                if ($cost['PCR'] == $plan) {
                    //echo $plan ." ==== ". $cost['PRICE'] ."<br>";
                    $tot_cost = $tot_cost + $cost['PCR_PRICE'];
                }
                if ($cost['TPA'] == $plan) {
                    //echo $plan ." ==== ". $cost['PRICE'] ."<br>";
                    $tot_cost = $tot_cost + $cost['TPA_PRICE'];
                }      
                if ($cost['ADDT'] > 0) {
                    //echo $plan ." ==== ". $cost['PRICE'] ."<br>";
                    $tot_cost = $tot_cost + $cost['ADDT'];
                }                           
            }

            $tmpArr =  array (
                'PLAN' => $plan,
                'COUNT' => $count,
                'COST' => $tot_cost,
            );
            array_push($invoice_arr, $tmpArr); //prepare costing for each record

            $tot_ecert = $tot_ecert + $tot_cost;
        }
        echo "<br><br>INVOICE====<br>";
        print_r($invoice_arr);
        die();

        foreach ($pcr_arr as $plan => $tot_count) {
            echo $plan .' === '. $tot_count ."<br>";
            $tot_pcr = 0.00;
            foreach ($costing_arr as $cost) {
                if ($cost['PCR'] == $plan) {
                    //echo $plan ." ==== ". $cost['COST'] ."<br>";
                    $tot_pcr = $tot_pcr + $cost['PCR_PRICE'];
                }
            }

            $tmpArr =  array (
                'PLAN' => $plan,
                'COUNT' => $tot_count,
                'PCR_PRICE' => $tot_pcr,
            );
            array_push($invoice_arr, $tmpArr); //prepare costing for each record

            $tot_ecert = $tot_ecert + $tot_cost;
            //echo "TOT_COST=".$tot_cost."<br>";
            //echo "TOT_PLAN=".$tot_ecert."<br>";
        }

        foreach ($tpa_arr as $plan => $tot_count) {
            echo $plan .' === '. $tot_count ."<br>";
        }








        $tot_inv = $tot_ecert + $tot_pcr + $tot_tpa;

        die();
        return view('payment.payment', compact('plan_arr',  'plans', 'id', 'invoice_arr', 'tot_inv', 'tot_rec', 'pcr_arr', 'tpa_arr'));
    }

    public function submit_payment(Request $request)
    {
        $dt = Carbon::now();

        $file = $request->pay_file;
        $filename = $file->getClientOriginalName();

        $payment =  new Payment;
        $payment->pay_date = $dt->toDateString();
        $payment->pay_by = request()->post('pay_by');
        $payment->pay_file = $filename;
        $payment->pay_total = request()->post('pay_total');
        $payment->file_id = request()->post('id');
        $payment->save();

        $file = FileUpload::where('id', request()->post('id'))->first();
        $request->pay_file->storeAs(
            $file->user_id.'/payment/'.request()->post('id'), $filename
        );

        $upload = FileUpload::where('id', request()->post('id'))->first();
        $upload->status = '4';
        $upload->save();

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

    public function create_invoice_ind($order_id) {
        $user = Order::where([['id', '=' ,$order_id],['status', '1']])->first();
        // $user = DashboardUser::where('id', $orders->user_id)->first();
        $dt = Carbon::now();
        $date_today = $dt->toDateString();
        $plan = Plan::where('name', $user->plan_type)->first();

        $dataTable = new \stdClass();
        $dataTable->quantity = '1';
        $dataTable->description = $plan->description;
        $dataTable->unit_price = $plan->price*1;
        $dataTable->amount = $plan->price*1;

        $amount = $plan->price*1;

        $tables = array($dataTable);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('payment.invoice', compact('user', 'date_today', 'tables', 'amount'));
        return $pdf->stream();
    }

    public function create_invoice($order_id) {
        $orders = Order::where([['id', '=' ,$order_id],['status', '1']])->first();
        $user = DashboardUser::where('id', $orders->user_id)->first();
        $dt = Carbon::now();
        $date_today = $dt->toDateString();
        $plan = Plan::where('name', $orders->plan_type)->first();

        $dataTable = new \stdClass();
        $dataTable->quantity = '1';
        $dataTable->description = $plan->description;
        $dataTable->unit_price = $plan->price*1;
        $dataTable->amount = $plan->price*1;
        
        $tables = array($dataTable);
        $amount = $plan->price*1;

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('payment.invoice', compact('orders', 'date_today', 'user', 'tables',  'amount'));
        return $pdf->stream();
    }

    public function create_cert_ind($order_id) {
        $orders = Order::where([['id', '=' ,$order_id],['status', '1']])->first();
        $plan = Plan::where('name', $orders->plan_type)->first();

        $cert_number = $orders->ecert;

        // $user = DashboardUser::where('id', $orders->user_id)->first();
        // $dt = Carbon::now();
        // $date_today = $dt->toDateString();
        // $plan = Plan::where('name', $orders->plan_type)->first();

        // $dataTable = new \stdClass();
        // $dataTable->quantity = '1';
        // $dataTable->description = $plan->description;
        // $dataTable->unit_price = $plan->price*1;
        // $dataTable->amount = $plan->price*1;
        
        // $tables = array($dataTable);
        // $amount = $plan->price*1;

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('payment.e-cert', compact('orders', 'plan', 'cert_number'));
        return $pdf->stream();
    }
}
