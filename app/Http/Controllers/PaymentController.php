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

        $invoice_num = null;
        if ($orders && $orders[0]) {
            $invoice_num = $orders[0]->invoice;
        }

        return view('payment.payment', compact('plan_arr',  'plans', 'id', 'invoice_arr', 'tot_inv', 'tot_rec', 'tpa_arr', 'tpa_total_arr', 'pcr_detail', 'invoice_num'));
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
            array_push($invoice_arr, $tmpArr); //prepare costing for each record
            $tot_tpa = $tot_tpa + $tpa_cost;
        }
        $tot_inv = $tot_ecert + $pcrArr['COST'] + $tot_tpa;
        $dt = Carbon::now();
        $date_today = $dt->toDateString();

        $invoice_num = null;
        if ($orders && $orders[0]) {
            $invoice_num = $orders[0]->invoice;
        }

        $files = FileUpload::where('id', $order_id)->first();
        if ($pcrArr['COST'] == 0 || $pcrArr['COST'] == '0') {
            
        } else {
            array_push($invoice_arr, $pcrArr);
        }
        
        // dd($invoice_arr);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('payment.invoice-all', compact('files', 'invoice_arr', 'tot_inv', 'tot_rec', 'tpa_arr', 'tpa_total_arr', 'date_today', 'invoice_num'));
        return $pdf->stream();
    }

    public function create_cert_ind($order_id) {
        $orders = Order::where([['id', '=' ,$order_id],['status', '1']])->first();
        $plan = Plan::where('name', $orders->plan_type)->first();
        $url_bg = Storage::path('template/template_cert.png');

        $cert_number = $orders->ecert;


        //fix birth date
        //dd($orders->ecert, $orders->dob, $orders->dep_date);
        $dob = new Carbon($orders->dob);
        $dobyear = 0 + $dob->format('Y');
        $nowyear = Carbon::now()->year;
        $correctyear = $dobyear;
        $newbirth = $dob;
        if ($dobyear > $nowyear) {
            $correctyear = $dobyear - 100;
            $newbirth = Carbon::create($correctyear, 0 + $dob->format('m'), 0 + $dob->format('d'));
        }
        //dd($orders->dob,  $dobyear, $nowyear, $correctyear, $newbirth->format('Y-m-d'));
        $newbirth = $newbirth->format('d-m-Y');


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
        $order = Order::where([['file_id', '=', $id], ['plan_type', '!=', 'NO']])->get();

        if ($order) ini_set('max_execution_time', '500');

        foreach ($order as $key => $orders) {
            $plan = Plan::where('name', $orders->plan_type)->first();
            $url_bg = Storage::path('template/template_cert.png');

            $cert_number = $orders->ecert;

            //fix birth date
            //dd($orders->ecert, $orders->dob, $orders->dep_date);
            $dob = new Carbon($orders->dob);
            $dobyear = 0 + $dob->format('Y');
            $nowyear = Carbon::now()->year;
            $correctyear = $dobyear;
            $newbirth = $dob;
            if ($dobyear > $nowyear) {
                $correctyear = $dobyear - 100;
                $newbirth = Carbon::create($correctyear, 0 + $dob->format('m'), 0 + $dob->format('d'));
            }
            //dd($orders->dob,  $dobyear, $nowyear, $correctyear, $newbirth->format('Y-m-d'));
            $newbirth = $newbirth->format('d-m-Y');


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
