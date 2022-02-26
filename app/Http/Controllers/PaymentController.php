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
use iio\libmergepdf\Merger;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['role:tra|akc|ag|ind|fin']);
    }
    
    public function payment($id)
    {
        $tot_rec = 0;
        $uploads = FileUpload::where('id', $id)->first();
        $orders = Order::where([['file_id', '=' ,$id],['status', '1']])->get();
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

                // //calculate for PCR
                // $pcr = 0.00;   //pcr price
                // $pcr_name = 'PCR';
                // if ($order->pcr == 'YES' || $order->pcr == 'PCR') {
                //     $pcr = $pcr + $price_pcr;
                //     $pcr_cnt = $pcr_cnt + 1;
                //     array_push($pcr_arr, $pcr_name);
                // }

                // //calculate for TPA
                // $tpa_name = null;
                // $tpa_price = 0.00;
                // if ($order->tpa!=null && $order->tpa!='' && $order->tpa!='NO') {
                //     $tpa_name = $order->tpa;
                //     $plan_tpa_price = Plan::where([['name', '=' ,$tpa_name]])->pluck('price')->first();
                //     if ($plan_tpa_price && $plan_tpa_price > 0.00) {
                //         $tpa_price = $plan_tpa_price;
                //     }

                //     array_push($tpa_arr, $tpa_name);   //grouping the selected plans
                // }


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
                $tmpArr =  array (
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
        
        $tot_pcr = (isset($pcr_arr['PCR']) ? $pcr_arr['PCR'] : 0) * $price_pcr;
        $pcr_detail = new \stdClass();
        $pcr_detail->name = 'PCR';
        $pcr_detail->price = $tot_pcr;
        $pcr_detail->cnt = (isset($pcr_arr['PCR']) ? $pcr_arr['PCR'] : 0);

        $invoice_arr = array();
        foreach ($plan_arr as $plan => $tot_count) {
            $tot_cost = 0.00;
            foreach ($costing_arr as $cost) {
                if ($cost['PLAN'] == $plan) {
                    $tot_cost = $tot_cost + $cost['COST'];
                }
            }

            $tmpArr =  array (
                'PLAN' => $plan,
                'PCR_COUNT' => $pcr_cnt,
                'PCR_TOT' => $tot_pcr,
                'COUNT' => $tot_count,
                'COST' => $tot_cost,
            );
            array_push($invoice_arr, $tmpArr); //prepare costing for each record

            $tot_ecert = $tot_ecert + $tot_cost;
        }

        $tpa_total_arr = array();
        foreach ($tpa_arr as $tpa => $tot_count) {
            $tpa_cost = 0.00;
            foreach ($costing_arr as $cost) {
                if ($cost['TPANAME'] == $tpa) {
                    $tpa_cost = $tpa_cost + $cost['TPA'];
                }
            }

            $tmpArr =  array (
                'PLAN' => $tpa,
                'COUNT' => $tot_count,
                'COST' => $tpa_cost,
            );
            array_push($tpa_total_arr, $tmpArr); //prepare costing for each record
            $tot_tpa = $tot_tpa + $tpa_cost;
        }
        $tot_inv = $tot_ecert + $tot_pcr + $tot_tpa;

        //discard comma in value --
        $tot_inv = $tot_inv - $uploads->discount;
        // if ($uploads->discount) {
        //     $discount = str_replace(',', '', ''.$uploads->discount);
        //     $tot_inv = $tot_inv - $discount;
        // }


        $invoice_num = null;
        if ($orders && $orders[0]) {
            $invoice_num = $orders[0]->invoice;
        }

        return view('payment.payment', compact('uploads', 'plan_arr',  'plans', 'id', 'invoice_arr', 'tot_inv', 'tot_rec', 'tpa_arr', 'tpa_total_arr', 'pcr_detail', 'invoice_num'));
    }

    public function submit_payment(Request $request)
    {
        if (request()->post('pay_by') == 'cc') {
            return redirect()->route('stripe', ['pay_id' => request()->post('id'), 'pay_total' => request()->post('pay_total'), 'pay_name' => 'cc']);
        } else if (request()->post('pay_by') == 'fpx') {
            return redirect()->route('stripe', ['pay_id' => request()->post('id'), 'pay_total' => request()->post('pay_total'), 'pay_name' => 'fpx']);
        }

        $dt = Carbon::now();

        $file = $request->pay_file;
        if ($file){
            $filename = $file->getClientOriginalName();
        } else {
            $filename = null;
        }

        $payment =  new Payment;
        $payment->pay_date = $dt->toDateString();
        $payment->pay_by = request()->post('pay_by');
        $payment->pay_file = $filename;
        $payment->pay_total = request()->post('pay_total');
        $payment->file_id = request()->post('id');
        $payment->save();

        $file = FileUpload::where('id', request()->post('id'))->first();
        if ($filename){
            $request->pay_file->storeAs(
                $file->user_id.'/payment/'.request()->post('id'), $filename
            );

            try {
                Storage::deleteDirectory($file->user_id.'/supp_doc/'.request()->post('id').'/payreceipt');
            }
            catch(\Exception $ex) {
                //
            }            

            $request->pay_file->storeAs(
                $file->user_id.'/supp_doc/'.request()->post('id').'/payreceipt', $filename
            );
        }

        $upload = FileUpload::where('id', request()->post('id'))->first();
        $upload->status = '4';
        $supp_doc = '';
        if ($upload->supp_doc != null) $supp_doc = $upload->supp_doc;
        $supp_doc = str_replace("R", "", $supp_doc)."R";
        $upload->supp_doc = $supp_doc;
        $upload->save();

        $user = DashboardUser::where('id', $file->user_id)->first();
        app('App\Http\Controllers\EmailController')->send_mail('Invoice', $user->name, $user->email, 'PAID, waiting for AKC endorsement', 'Payment');

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
        // $users = DashboardUser::where('id', $user->user_id)->first();

        $dt = Carbon::now();
        $date_today = $dt->toDateString();
        $plan = Plan::where('name', $user->plan_type)->first();
        $tpa = Plan::where('name', $user->tpa)->first();

        $ecare = new \stdClass();
        $ecare->quantity = '1';
        $ecare->description = $plan->description;
        $ecare->unit_price = $plan->price*1;
        $ecare->amount = $plan->price*1;

        $tables = array($ecare);
        
        $tpa = Plan::where('name', $user->tpa)->first();
        $tpa_val = new \stdClass();
        $tpa_val->quantity = '1';
        $tpa_val->description = $tpa ? $tpa->description : '';
        $tpa_val->unit_price = $tpa ? $tpa->price*1 : '';
        $tpa_val->amount = $tpa ? $tpa->price*1 : '';

        $pcr = Plan::where('name', 'pcr')->first();
        $pcr_val = new \stdClass();
        $pcr_val->quantity = '1';
        $pcr_val->description = $pcr ? $pcr->description : '';
        $pcr_val->unit_price = $pcr ? $pcr->price*1 : '';
        $pcr_val->amount = $pcr ? $pcr->price*1 : '';

        if ($tpa) {
            array_push($tables, $tpa_val);
        }

        if ($pcr) {
            array_push($tables, $pcr_val);
        }

        $amount = ($plan->price*1) + ($tpa ? $tpa->price*1 : 0) + ($pcr ? $pcr->price*1 : 0);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('payment.invoice', compact('user', 'date_today', 'tables', 'amount'));
        return $pdf->stream($user->invoice);
    }

    public function create_invoice($order_id) {
        $files = FileUpload::where('id', $order_id)->first();

        if ($files->json_inv) {
            $data_decode = json_decode($files->json_inv, true);

            $tpa_pcr_arr = $data_decode['tpa_pcr_arr'];
            // $files = (object)$data_decode['files'];
            // dd($files->id);
            $invoice_arr = collect($data_decode['invoice_arr']);
            $tot_inv = $data_decode['tot_inv'];
            $tot_inv2 = $data_decode['tot_inv2'];

            //$disArr = collect($data_decode['disArr']);
            $disArr = ($data_decode['disArr'] && $data_decode['disArr']!=null? collect($data_decode['disArr']) : null);

            $tot_rec = collect($data_decode['tot_rec']);
            $tpa_arr = collect($data_decode['tpa_arr']);
            $tpa_total_arr = collect($data_decode['tpa_total_arr']);
            $date_today = $data_decode['date_today'];
            $invoice_num = $data_decode['invoice_num'];
            $tpa_pcr_arr = collect($data_decode['tpa_pcr_arr']);

            //dd($files->json_inv, $data_decode, $data_decode['disArr']);


        } else {
            $tot_rec = 0;
            $orders = Order::where([['file_id', '=' ,$order_id],['status', '1']])->get();
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
                    $tmpArr =  array (
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

            $pcrArr =  array (
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

                $tmpArr =  array (
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

                $tmpArr =  array (
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

            // if ($files->discount !== '0') {
            //     $tmpArr =  array (
            //         'PLAN' => 'DISCOUNT',
            //         'PRICE' => "-".$files->discount,
            //         'COUNT' => '1',
            //         'COST' => "-".$files->discount,
            //     );
            //     array_push($invoice_arr, $tmpArr);
            // }
            $disArr = null;
            if ($files->discount && $files->discount !== '0') {
                $disArr = array(
                    'PLAN' => 'DISCOUNT',
                    'PRICE' => $files->discount,
                    'COUNT' => '1',
                    'COST' => $files->discount,
                );
            }


            //$tot_inv = $tot_ecert - ($disArr ? $disArr['COST'] : 0);
            $tot_inv = $tot_ecert;
            if ($disArr && $disArr['COST']) {
                $tot_inv = $tot_ecert - $disArr['COST'];
                
                //$discount = str_replace(',', '', ''.$disArr['COST']);
                //$tot_inv = $tot_ecert - $discount;
            }

            $tot_inv2 = $tot_inv + ($disArr ? $disArr['COST'] : 0) + $tot_tpa;
        }
        

        //dd($tot_inv, $tot_inv2, $disArr);
        
        // dd($invoice_arr);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('payment.invoice-all', compact('tpa_pcr_arr','files', 'invoice_arr', 'tot_inv', 'tot_inv2', 'disArr', 'tot_rec', 'tpa_arr', 'tpa_total_arr', 'date_today', 'invoice_num'));
        return $pdf->stream();
    }

    public function create_cert_ind($order_id) {
        $orders = Order::where([['id', '=' ,$order_id],['status', '1']])->first();
        $plan = Plan::where('name', $orders->plan_type)->first();
        $url_bg = Storage::path('template/template_cert.png');

        $cert_number = $orders->ecert;

        //fix birth date
        //dd($orders->ecert, $orders->dob, $orders->dep_date);
        // if (str_contains($orders->dob, '/')) {
        //     $dob_data = str_replace("/","-", $orders->dob);
        // } else {
        //     $dob_data = date("m-d-Y", strtotime($orders->dob));
        // }
        
        // $dob = new Carbon($orders->dob);
        // $dobyear = 0 + $dob->format('Y');
        // $nowyear = Carbon::now()->year;
        // $correctyear = $dobyear;
        // $newbirth = $dob;
        // if ($dobyear > $nowyear) {
        //     $correctyear = $dobyear - 100;
        //     $newbirth = Carbon::create($correctyear, 0 + $dob->format('m'), 0 + $dob->format('d'));
        // }
        //dd($orders->dob,  $dobyear, $nowyear, $correctyear, $newbirth->format('Y-m-d'));
        $newbirth = $orders->dob;


        //fix plan duration date
        // $total_days = $plan->total_days;
        // $addDays = (0 + $total_days) - 1;
        $depdate = new Carbon($orders->dep_date);
        $rtndate = new Carbon($orders->return_date);
        // $rtndate->addDays($addDays);
        $duration = "(".$depdate->format('d-m-Y').") TO (".$rtndate->format('d-m-Y').")";

        //dd($plan->name, $total_days, $orders->dep_date, $addDays, $depdate, $duration);

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
        $pdf->loadView('payment.e-cert', compact('orders', 'plan', 'cert_number', 'url_bg', 'newbirth', 'duration'));
        return $pdf->stream();
    }

    public function ecert_all ($id) {
        $order = Order::where([['file_id', '=', $id], ['plan_type', '!=', 'NO'],  ['status', '=', '1']])->get();

        if ($order) ini_set('max_execution_time', '500');

        foreach ($order as $key => $orders) {
            $plan = Plan::where('name', $orders->plan_type)->first();
            $url_bg = Storage::path('template/template_cert.png');

            $cert_number = $orders->ecert;

            //fix birth date
            //dd($orders->ecert, $orders->dob, $orders->dep_date);
            // $dob = new Carbon($orders->dob);
            // $dobyear = 0 + $dob->format('Y');
            // $nowyear = Carbon::now()->year;
            // $correctyear = $dobyear;
            // $newbirth = $dob;
            // if ($dobyear > $nowyear) {
            //     $correctyear = $dobyear - 100;
            //     $newbirth = Carbon::create($correctyear, 0 + $dob->format('m'), 0 + $dob->format('d'));
            // }
            //dd($orders->dob,  $dobyear, $nowyear, $correctyear, $newbirth->format('Y-m-d'));
            $newbirth = $orders->dob;


            //fix plan duration date
            //$total_days = $plan->total_days;
            //$addDays = (0 + $total_days) - 1;
            $depdate = new Carbon($orders->dep_date);
            $rtndate = new Carbon($orders->return_date);
            //$rtndate->addDays($addDays);
            $duration = "(".$depdate->format('d-m-Y').") TO (".$rtndate->format('d-m-Y').")";
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadView('payment.e-cert', compact('orders', 'plan', 'cert_number', 'url_bg', 'newbirth', 'duration'));
            $content = $pdf->download()->getOriginalContent();

            Storage::put(Auth::id().'/ecert/'.$id.'/'.$orders->passport_no.'.pdf',$content);
        }
        
        $pdf_id = Order::where([['file_id', '=', $id], ['plan_type', '!=', 'NO']])->get();
        $tmpArr = array();
        foreach ($pdf_id as $key => $pdf) {
            array_push($tmpArr, Storage::path(Auth::id().'/ecert/'.$id.'/'.$pdf->passport_no.'.pdf'));
        }
        // dd($tmpArr);
        $merger = new Merger;
        $merger->addIterator($tmpArr);
        
        $createdPdf = $merger->merge();
        Storage::put(Auth::id().'/ecert/'.$id.'/merged.pdf',$createdPdf);
        
        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Merged!'
        ], 200); // Status code here
        // return $pdf->stream();
    }    



    public function ecert_getall ($id) {
        $order = Order::where([['file_id', '=', $id], ['plan_type', '!=', 'NO'],  ['status', '=', '1']])->get();

        $max_count = 30;
        $divide = 0;
        $remain = 0;
        $pages = 0;

        if ($order) {
            $tot_count = count($order);
            $divide = (int) ($tot_count / $max_count);
            $remain = (int) ($tot_count % $max_count);
            if ($remain>0) $pages = 1 + $divide;
            
            $pages_arr = array();
            for ($x = 0; $x < $pages; $x++) {
                $balance = (($tot_count-($x+1)*$max_count)>0 ? (($x+1)*$max_count) : (($x+1)*$max_count)-((($x+1)*$max_count)-$tot_count));
                $range = ''. (($x*$max_count)+1) .' - '. $balance;
                $tmpArr = array (
                    'id' => $id,
                    'total' => $tot_count,
                    'max' => $max_count,
                    'page' => $x+1,
                    'range' => $range,
                );
                array_push($pages_arr, $tmpArr);
            }

            //$order1 = Order::where([['file_id', '=', $id], ['plan_type', '!=', 'NO'],  ['status', '=', '1']])->offset(0*30)->limit(30)->get();
            //$order2 = Order::where([['file_id', '=', $id], ['plan_type', '!=', 'NO'],  ['status', '=', '1']])->offset(1*30)->limit(30)->get();
            //dd(count($order), $divide, $remain, $pages, count($order1), count($order2), $order1[29]->ecert, $order2[0]->ecert, $pages_arr);
            //dd(count($order), $divide, $remain, $pages, $pages_arr);
            return response()->json([
                'isSuccess' => true,
                'Data' => 'Successfully Merged!',
                'pages' => $pages_arr
            ], 200);
        }

        return response()->json([
            'isSuccess' => false,
            'Data' => 'No Data Found!',
            'pages' => null
        ], 200); // Status code here
        // return $pdf->stream();
    }

    public function ecert_getall_page ($id, $page) {
        $max_count = 30;
        $page = (0 + $page) - 1;
        //dd($id, $page, $max_count);

        if ($page<0) {
            return response()->json([
                'isSuccess' => false,
                'Data' => 'No Data Found!',
                'pages' => null
            ], 200); // Status code here
        }

        //$order = Order::where([['file_id', '=', $id], ['plan_type', '!=', 'NO'],  ['status', '=', '1']])->get();
        $order = Order::where([['file_id', '=', $id], ['plan_type', '!=', 'NO'],  ['status', '=', '1']])->offset($page*$max_count)->limit($max_count)->get();
        //dd($id, $page, $max_count, count($order), $order);

        if ($order) {
            ini_set('max_execution_time', '500');
        } 
        else {
            return response()->json([
                'isSuccess' => false,
                'Data' => 'No Data Found!',
                'pages' => null
            ], 200); // Status code here
        }

        foreach ($order as $key => $orders) {
            $plan = Plan::where('name', $orders->plan_type)->first();
            $url_bg = Storage::path('template/template_cert.png');

            $cert_number = $orders->ecert;
            $newbirth = $orders->dob;

            $depdate = new Carbon($orders->dep_date);
            $rtndate = new Carbon($orders->return_date);
            //$rtndate->addDays($addDays);

            $duration = "(".$depdate->format('d-m-Y').") TO (".$rtndate->format('d-m-Y').")";
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadView('payment.e-cert', compact('orders', 'plan', 'cert_number', 'url_bg', 'newbirth', 'duration'));
            $content = $pdf->download()->getOriginalContent();

            Storage::put(Auth::id().'/ecert/'.$id.'/'.$orders->passport_no.'.pdf',$content);
        }
        
        //$pdf_id = Order::where([['file_id', '=', $id], ['plan_type', '!=', 'NO']])->get();
        $pdf_id = Order::where([['file_id', '=', $id], ['plan_type', '!=', 'NO'],  ['status', '=', '1']])->offset($page*$max_count)->limit($max_count)->get();

        $tmpArr = array();
        foreach ($pdf_id as $key => $pdf) {
            array_push($tmpArr, Storage::path(Auth::id().'/ecert/'.$id.'/'.$pdf->passport_no.'.pdf'));
        }
        // dd($tmpArr);
        $merger = new Merger;
        $merger->addIterator($tmpArr);

        $merged_name = "merged-".$id.'-'.(($page*$max_count)+1).'-'.count($pdf_id).'.pdf';
        
        $createdPdf = $merger->merge();
        //Storage::put(Auth::id().'/ecert/'.$id.'/merged.pdf',$createdPdf);
        Storage::put(Auth::id().'/ecert/'.$id.'/'.$merged_name,$createdPdf);

        return response()->file(Storage::path(Auth::id().'/ecert/'.$id.'/'.$merged_name), [ 'Content-Disposition' => 'filename='.$merged_name ]);
        
        // return response()->json([
        //     'isSuccess' => true,
        //     'Data' => 'Successfully Merged!'
        // ], 200); // Status code here
        //return $createdPdf->stream();
    }   






    public function download_all_cert ($id) {
        // Storage::deleteDirectory(Auth::id().'/ecert/'.$id);
        return Storage::download('/'.Auth::id().'/ecert/'.$id.'/merged.pdf');
    }

    public function delete_all_cert ($id) {
        Storage::deleteDirectory(Auth::id().'/ecert/'.$id);

        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Cleaned!',
        ], 200);
    }
}
