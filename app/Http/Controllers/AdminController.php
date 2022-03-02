<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\DashboardUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use DB;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use File;
use Response;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['role:akc|fin']);
    }

    public function admin_payment_detail($id)
    {
        $uploads = FileUpload::where('id', $id)->first();
        $pay = Payment::where('file_id', $id)->first();

        $invoice_num = '';
        //dd($uploads);

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

            $invoice_num = $order->invoice;
            //dd($order);

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
        
        $tot_pcr = (isset($pcr_arr['PCR']) ? $pcr_arr['PCR'] : 0)  * $price_pcr;
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
        $tot_inv = $tot_inv - $uploads->discount;

        //dd($invoice_num);

        return view('admin.invoice', compact('uploads', 'pay', 'plan_arr', 'plans', 'invoice_arr', 'tot_inv', 'tot_rec', 'tpa_total_arr', 'pcr_detail', 'invoice_num'));
    }

    public function excel_list_admin()
    {
        //$uploads = FileUpload::all();
        $uploads = FileUpload::where('status','!=','0')->orderBy('submit_date', 'DESC')->orderBy('status', 'ASC')->get();
        //dd($uploads);
        $users = DashboardUser::all();

        //include number of records count
        $rec_count_arr = array();
        $suppdoc_arr = array();
        $stats_arr = array();

        $pending1 = 0;
        $pending2 = 0;
        $pending3 = 0;
        $pending4 = 0;

        if ($uploads) {
            foreach ($uploads as $upload) {
                $count = 0;
                $orders = Order::where([['file_id', '=' ,$upload->id]])->get();
                if ($orders) {
                    $count = count($orders);
                }
                array_push($rec_count_arr, $count); //prepare costing for each record

                if ($upload->status == '2' || $upload->status == '2.2' || $upload->status == '2.3') $pending1 = $pending1 + 1;
                if ($upload->status == '2.1') $pending2 = $pending2 + 1;
                if ($upload->status == '3') $pending3 = $pending3 + 1;
                if (!$upload->supp_doc) $pending4 = $pending4 + 1;
                
                //fuad: get the supp_doc data
                if ($upload->supp_doc) {
                    $flink = null;
                    $fname = null;
                    //dd($uploads, $upload, $upload->supp_doc, stripos($upload->supp_doc, 'P'));
                    if (stripos($upload->supp_doc, 'P') >=0 ) {

                        $directory = '/'.$upload->user_id.'/supp_doc/'.$upload->id.'/passport';
                        $files = Storage::allFiles($directory);
                        // $url = Storage::url($files[0]);
                        //dd($files, $url);

                        if (empty($files)) { 
                            array_push($suppdoc_arr, null);
                        } 
                        else {
                            $files = collect(Storage::allFiles($directory));
                            $flink = $files[0];
                            $fname = str_ireplace($upload->user_id.'/supp_doc/'.$upload->id.'/passport'.'/', '', $flink);
                            $tmpArr = array (
                                'id' => $upload->id,
                                'fname' => $fname,
                                'flink' => $flink,
                            );
                            array_push($suppdoc_arr, $tmpArr);
                        }
                    }
                    //dd($directory, $flink, $fname, $suppdoc_arr);

                    $flink = null;
                    $fname = null;
                    if (stripos($upload->supp_doc, 'T') >=0 ) {
                        $directory = '/'.$upload->user_id.'/supp_doc/'.$upload->id.'/eticket';
                        $files = Storage::allFiles($directory);
                        if (empty($files)) { 
                            array_push($suppdoc_arr, null);
                        } 
                        else {
                            $files = collect(Storage::allFiles($directory));
                            $flink = $files[0];
                            $fname = str_ireplace($upload->user_id.'/supp_doc/'.$upload->id.'/eticket'.'/', '', $flink);
                            $tmpArr = array (
                                'id' => $upload->id,
                                'fname' => $fname,
                                'flink' => $flink,
                            );
                            array_push($suppdoc_arr, $tmpArr);
                        }
                    }

                    $flink = null;
                    $fname = null;
                    if (stripos($upload->supp_doc, 'V') >=0 ) {
                        $directory = '/'.$upload->user_id.'/supp_doc/'.$upload->id.'/visa';
                        $files = Storage::allFiles($directory);
                        if (empty($files)) { 
                            array_push($suppdoc_arr, null);
                        } 
                        else {
                            $files = collect(Storage::allFiles($directory));
                            $flink = $files[0];
                            $fname = str_ireplace($upload->user_id.'/supp_doc/'.$upload->id.'/visa'.'/', '', $flink);
                            $tmpArr = array (
                                'id' => $upload->id,
                                'fname' => $fname,
                                'flink' => $flink,
                            );
                            array_push($suppdoc_arr, $tmpArr);
                        }
                    }

                    $flink = null;
                    $fname = null;
                    if (stripos($upload->supp_doc, 'R') >=0 ) {
                        $directory = '/'.$upload->user_id.'/supp_doc/'.$upload->id.'/payreceipt';
                        $files = Storage::allFiles($directory);
                        if (empty($files)) { 
                            array_push($suppdoc_arr, null);
                        } 
                        else {
                            $files = collect(Storage::allFiles($directory));
                            $flink = $files[0];
                            $fname = str_ireplace($upload->user_id.'/supp_doc/'.$upload->id.'/payreceipt'.'/', '', $flink);
                            $tmpArr = array (
                                'id' => $upload->id,
                                'fname' => $fname,
                                'flink' => $flink,
                            );
                            array_push($suppdoc_arr, $tmpArr);
                        }
                    }
                }
            }
        }
        //dd($rec_count_arr);
        //dd($suppdoc_arr);

        $stats_arr = array(
            'pending1' => $pending1,
            'pending2' => $pending2,
            'pending3' => $pending3,
            'pending4' => $pending4,
        );

        return view('admin.excel-list', compact('uploads', 'users', 'rec_count_arr', 'suppdoc_arr', 'stats_arr'));
    }

    public function excel_detail_admin($id)
    {
        $orders = Order::where('file_id', $id)->get();
        $uploads = FileUpload::where('id', $id)->first();
        $payment = Payment::where('file_id', $id)->first();

        //--
        $additional_arr = array();
        foreach ($orders as $order) {

            if ($order->plan_type!=null && $order->plan_type!='' && $order->plan_type != 'NO') {
                $date1 = date_create($order->dep_date);
                $date2 = date_create($order->return_date);
                $diff = date_diff($date1, $date2);

                $days = $diff->days;
                $maxday = Plan::where([['name', '=' ,$order->plan_type]])->pluck('total_days')->first();

                $difday = 0;
                if ($days && $maxday && ($days > $maxday)) {
                    $difday = ($maxday - $days);
                    $difday = ($difday>0?$difday:($difday*-1));
                }

                //prepare costing array
                $tmpArr = array (
                    //'PLAN' => $order->plan_type,
                    //'PRICE' => $price,
                    //'DEP' => $date1,
                    //'RTN' => $date2,
                    //'DAYS' => $days,
                    //'MAXDAY' => $maxday,
                    'DIFDAY' => ($difday == 0? "(0)" : "(+".$difday.")"),
                    //'PERDAY' => $perday,
                    //'COST' => $cost,
                    //'ADDT' => $addt,
                );
            }
            else {
                //prepare costing array
                $tmpArr =  array (
                    'DIFDAY' => "",
                );
            }
            array_push($additional_arr, $tmpArr);   //grouping the selected plans
        }

        $allFiles =  FileUpload::where('status', '5')->get();
        $allOrders = array ();
        foreach ($allFiles as $i => $file) {
            $ordersArr = Order::where([['file_id', '=', $file->id], ['plan_type', '!=', 'NO']])->get()->toArray();
            foreach ($ordersArr as $o => $arr) {
                array_push($allOrders, $arr);
            }
        }
        //--
        $last_order = end($allOrders);
        // $ecert_no = ;
        if ($last_order) {
            $year = Carbon::now()->year;
            $str1 = explode('A'.$year, $last_order['ecert']);
            $ecert_no = $str1[1];
        } else {
            $ecert_no = '0';
        }
        
        // dd($ecert_no);

        return view('admin.detail-excel', compact('orders', 'uploads', 'payment', 'additional_arr',  'ecert_no'));
    }

    public function excel_post_admin(Request $request)
    {
        $arr_json = request()->post('json_post');

        $dt = Carbon::now();
        
        $uploads = new FileUpload;
        $uploads->file_name = preg_replace('/\s+/', '', request()->post('file_name'));
        $uploads->upload_date = $dt->toDateString();
        //$uploads->status = '2.1';
        $uploads->status = '2';
        $uploads->ta_name = request()->post('travel_agent');
        $uploads->user_id = request()->post('user');
        $uploads->submit_date = $dt->toDateTimeString();
        $uploads->save();

        $collection = collect($request->all());

        $file = $collection['file'];
        $filename = $file->getClientOriginalName();

        $path = $collection['file']->storeAs(
            request()->post('user').'/excel/'.$uploads->id, preg_replace('/\s+/', '', request()->post('file_name'))
        );

        $url = Storage::path($uploads->user_id.'/excel/'.$uploads->id.'/'.$uploads->file_name);
        $inputFileName = $url;
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($url);
        $spreadsheet = $spreadsheet->getActiveSheet();
        $data_array =  $spreadsheet->toArray();

        unset($data_array[0]);

        //filter only available entry - checking row number availability
        $data_array = \Arr::where($data_array, function ($value, $key) {
            return $value[0]!=null && $value[0]!='';
        });
        //print_r($data_array);
        //die();

        $dt = Carbon::now();
        $orderdate = $dt->toDateString();
        $orderdate = explode('-', $orderdate);
        $year = $orderdate[0];
        $month = $orderdate[1];

        // dd($data_array);
        // foreach ($data_array as $i => $json) {
        //     $order = new Order;
        //     $order->name = $json[1];
        //     $order->passport_no = $json[2];
        //     $order->ic_no = $json[3];
        //     $order->dob = $json[4];
        //     $order->ex_illness = $json[5];
        //     $order->hp_no = $json[6];
        //     $order->plan_type = $json[7];
        //     $order->email = $json[8];
        //     $order->dep_date = $json[9];
        //     $order->return_date = $json[10];
        //     $order->pcr = $json[11] == 'PCR' ? 'YES' : 'NO';
        //     $order->tpa = $json[12];
        //     $order->user_id = $uploads->user_id;;
        //     $order->file_id = $uploads->id;
        //     $order->ecert = $uploads->id;
        //     $order->invoice = $uploads->id;
        //     $order->pcr_date = $json[10];
        //     $order->pcr_result = null;
        //     $order->pcr = $json[11];
        //     $order->tpa = $json[12];

        //     $order->save();

        //     $orders = Order::where('id', '=' ,$order->id)->first();
        //     $orders->ecert = 'A'.$year.$orders->id;
        //     //$orders->invoice = 'I'.$year.$orders->file_id.$orders->id;  
        //     $orders->invoice = $year.'/'.$month.'/'.$orders->file_id;  //fuad0602:change inv num: YYYY/MM/FILE_ID
        //     $orders->save();
        // }
        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Uploaded!'
        ], 200); // Status code here
    }

    public function update_excel_status_admin($id, $status)
    {
        $uploads = FileUpload::where('id', $id)->first();
        if ($status == '2.2') {
            $uploads->status = '2.1';
            $uploads->discount = 0;
            $uploads->percent = null;
        } else if ($status == '2.3') {
            $uploads->status = '2.1';
            $uploads->discount = 0;
            $uploads->percent = null;
        } else {
            $uploads->status = $status;
            //dd($uploads);

            if ($uploads->file_name) {
                //get excel file
                $url = Storage::path($uploads->user_id.'/excel/'.$uploads->id.'/'.$uploads->file_name);
                $inputFileName = $url;
                //dd($inputFileName);

                //create spreadsheet data reader
                $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
                // Create the reader object
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                // Instruct the reader to just read cell data
                $reader->setReadDataOnly(true);
                // Load the file to read
                $spreadsheet = $reader->load($inputFileName);
                // Get the active sheet
                $ssheet = $spreadsheet->getActiveSheet();
                //dd($ssheet);
                //convert to php data array
                $data_array = $ssheet->toArray();

                //remove non-data and header rows
                unset($data_array[0]);
                unset($data_array[9]);
                //dd($data_array);
    
                //filter only available entry - checking row number availability
                $data_array = \Arr::where($data_array, function ($value, $key) {
                    return $value[0]!=null && $value[0]!='';
                });
                //print_r($data_array);
                //die();
    
                //create invoice date 
                $dt = Carbon::now();
                $orderdate = $dt->toDateString();
                $orderdate = explode('-', $orderdate);
                $year  = $orderdate[0];
                $month = $orderdate[1];
    
                //date checking errors
                $error_sts = false;
                $error_msg = '';

                //process data jemaah
                foreach ($data_array as $i => $json) {

                    //dd($json);
                    $order = new Order;
                    $order->name = $json[1];
                    $order->passport_no = $json[2];
                    $order->ic_no = $json[3];

                    //dob date
                    try {
                        //$order->dob = $json[4] ? Carbon::createFromFormat('m/d/Y', $json[4])->format('d-m-Y') : '';
                        $order->dob = ''.date('d-m-Y',\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($json[4]));
                    } catch (\Throwable $th) {
                        $order->dob = $json[4];
                    }
                    $order->ex_illness = $json[5];
                    $order->hp_no = $json[6];
                    $order->plan_type = $json[7];
                    $order->email = $json[8];

                    //dep date
                    try {
                        //$order->dep_date = $json[9] ? Carbon::createFromFormat('d/m/Y', $json[9])->format('d-m-Y') : '';
                        $order->dep_date = ''.date('d-m-Y',\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($json[9]));
                        //dd("try", $order->dep_date);
                    } catch (\Throwable $th) {
                        $order->dep_date = $json[9];
                        //dd("catch", $order->dep_date);

                        $tmp_date = $json[9];
                        $tmp_date = str_replace('/', '-', $tmp_date);
                        $explode = explode('-', $tmp_date);

                        if (count($explode)==3) {
                            if ((int)$explode[1]>0 && (int)$explode[1]<13 && (int)$explode[2]>2000) {
                                $tmp_date = ''. (int)$explode[0] .'-'. (int)$explode[1] .'-'. (int)$explode[2];
                                //dd($tmp_date);
                                $new_date = Carbon::createFromFormat('d-m-Y',  $tmp_date)->format('d-m-Y');
                                $new_date = strtotime($new_date);
    
                                if ($new_date<0) {
                                    $error_sts = true;
                                    $error_msg = 'DEP Date Incorrect Format. Expected dd-mm-yyyy format. Approve Process Failed!';
                                }
                                else {
                                    $order->dep_date = date('d-m-Y', $new_date);
                                }
                            }
                            else {
                                $error_sts = true;
                                $error_msg = 'DEP Date Incorrect Format. Expected dd-mm-yyyy format. Approve Process Failed!';
                            }
                        }
                        else {
                            $error_sts = true;
                            $error_msg = 'DEP Date Incorrect Format. Expected dd-mm-yyyy format. Approve Process Failed!';
                        }
                        //dd("catch", $order->dep_date, $tmp_date, $explode, $error_sts, $error_msg);
                    }

                    //rtn date
                    try {
                        //$order->dep_date = $json[9] ? Carbon::createFromFormat('d/m/Y', $json[9])->format('d-m-Y') : '';
                        $order->return_date = ''.date('d-m-Y',\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($json[10]));
                        //dd("try", $order->dep_date);
                    } catch (\Throwable $th) {
                        $order->return_date = $json[10];
                        //dd("catch", $order->return_date);

                        $tmp_date = $json[10];
                        $tmp_date = str_replace('/', '-', $tmp_date);
                        $explode = explode('-', $tmp_date);

                        if (count($explode)==3) {
                            if ((int)$explode[1]>0 && (int)$explode[1]<13 && (int)$explode[2]>2000) {
                                $tmp_date = ''. (int)$explode[0] .'-'. (int)$explode[1] .'-'. (int)$explode[2];
                                //dd($tmp_date);
                                $new_date = Carbon::createFromFormat('d-m-Y',  $tmp_date)->format('d-m-Y');
                                $new_date = strtotime($new_date);
    
                                if ($new_date<0) {
                                    $error_sts = true;
                                    $error_msg = 'RTN Date Incorrect Format. Expected dd-mm-yyyy format. Approve Process Failed!';
                                }
                                else {
                                    $order->return_date = date('d-m-Y', $new_date);
                                }
                            }
                            else {
                                $error_sts = true;
                                $error_msg = 'RTN Date Incorrect Format. Expected dd-mm-yyyy format. Approve Process Failed!';
                            }
                        }
                        else {
                            $error_sts = true;
                            $error_msg = 'RTN Date Incorrect Format. Expected dd-mm-yyyy format. Approve Process Failed!';
                        }
                        //dd("catch", $order->return_date, $tmp_date, $explode, $error_sts, $error_msg);
                    }    
                    
                    //if error, stop and abort
                    if ($error_sts) {
                        Session::flash('error', $error_msg);
                        return redirect()->back();
                    }


                    //pcr date
                    $order->pcr_date = $order->return_date;

                    $order->user_id = $uploads->user_id;
                    $order->file_id = $uploads->id;
                    $order->invoice = $uploads->id;

                    $order->pcr_result = null;
                    $order->pcr = $json[11];
                    $order->tpa = $json[12];

                    //$orders = Order::where('id', '=' ,$order->id)->first();
                    // $orders->ecert = 'A'.$year.$orders->id;
                    //$orders->invoice = 'I'.$year.$orders->file_id.$orders->id;
                    $order->invoice = $year.'/'.$month.'/'.$uploads->id;  //fuad0602:change inv num: YYYY/MM/FILE_ID

                    //dd($order);
                    $order->save();
                }                
            }
        }

        $uploads->save();

        if ($status == '99') {
            $body = 'Rejected';
        } else {
            $body = 'Approved';
        }

        //send noti-email
        $user = DashboardUser::where('id', $uploads->user_id)->first();
        // dd($user);
        app('App\Http\Controllers\EmailController')->send_mail('Excel Update', $user->name, $user->email, 'Your request has been '.$body, 'Excel Submission');
        
        Session::flash('success', 'Excel Submission Approved!');
        return redirect()->back();
    }

    public function user_list()
    {
        // $role = Role::create(['name' => 'mkh']);
        // $role = Role::where('id', 3)->first();
        // Auth::user()->assignRole($role);
        $roles = Role::whereIn('id', [1, 2, 4, 5, 6])->get();
        $users = DashboardUser::all();
        return view('admin.user-list', compact('users',  'roles'));
    }

    public function plan_list()
    {
        $plans = Plan::all();

        $orders = array();
        foreach ($plans as $i => $plan) {
            $planType = Order::where('plan_type', $plan->name)->get();

            if ($planType->isNotEmpty()) {
                $tempArr = array(
                    $plan->name => count($planType)
                );
                array_push($orders, $tempArr);
            }
        }

        foreach ($plans as $i => $plan) {
            $pcr = Order::where('pcr', $plan->name)->get();
            if ($pcr->isNotEmpty()) {
                $tempArr = array(
                    $plan->name => count($pcr)
                );
                array_push($orders, $tempArr);
            }
        }

        foreach ($plans as $i => $plan) {
            $tpa = Order::where('tpa', $plan->name)->get();

            if ($tpa->isNotEmpty()) {
                $tempArr = array(
                    $plan->name => count($tpa)
                );
                array_push($orders, $tempArr);
            }
        }


        // dd($orders);
        return view('admin.plan', compact('plans', 'orders'));
    }

    public function plan_add()
    {
        return view('admin.plan-add');
    }

    public function plan_delete($id)
    {
        $plan = Plan::where('id', $id)->first();
        $order = Order::where('plan_type', $plan->name)->get();
        if(count($order) === 0) {
            $plan->delete();
            return redirect()->route('plan_list');
        } else {
            return redirect()->route('plan_list')->withErrors(['msg' => 'Error some user is using this plan']);
        }
    }

    public function post_plan(Request $request)
    {
        $plans = new Plan;
        $plans->name = request()->post('plan_name');
        $plans->description = request()->post('plan_desc');
        $plans->price = request()->post('plan_price');
        $plans->price_per_day = request()->post('price_per_day');
        $plans->total_days = request()->post('total_days');

        $plans->save();

        return redirect()->route('plan_list');
    }

    public function post_role($role_id, $user_id)
    {
        $users = DashboardUser::where('id', $user_id)->first();
        $roles = Role::whereIn('id', [1, 2, 4])->get();
        foreach ($roles as $i => $role) {
            $users->removeRole($role);
        }

        $role = Role::where('id', $role_id)->first();
        $users->assignRole($role);

        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Updated!'
        ], 200); // Status code here
    }

    public function download_supp_doc($user_id, $file_id)
    {
        return Storage::download('/'.$user_id.'/supp_doc/'.$file_id.'/Supporting_Document.rar');
    }

    public function download_excel($id)
    {
        $uploads = FileUpload::where('id', $id)->first();

        return Storage::download('/'.$uploads->user_id.'/excel/'.$uploads->id.'/'.$uploads->file_name);
    }

    public function setting_admin ()
    {
        $url = Storage::url('/template/AKC-ECARE-TEMPLATE-v1.0.xlsx');
        $url_bg = Storage::path('template/template_cert.png');
        // dd($url_bg);
        // mkdir('storage/app/public/template', 0755, true);
        return view('admin.setting', compact('url', 'url_bg'));
    }

    public function change_ecert_background(Request $request)
    {
        if (Storage::url('/template/template_cert.png')) {
            Storage::deleteDirectory('/template/template_cert.png');
        }
        $collection = collect($request->all());
        $ext = $collection['img']->extension();
        $path = $collection['img']->storeAs(
            'template/', 'template_cert.'.$ext
        );

        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Updated!'
        ], 200);
    }

    public function change_excel_template(Request $request)
    {
        if (Storage::url('/template/AKC-ECARE-TEMPLATE-v1.0.xlsx')) {
            Storage::deleteDirectory('/template/AKC-ECARE-TEMPLATE-v1.0.xlsx');
        }

        $collection = collect($request->all());
        $ext = $collection['excel']->extension();
        $path = $collection['excel']->storeAs(
            'template/', 'AKC-ECARE-TEMPLATE-v1.0.'.$ext
        );

        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Updated!'
        ], 200);
    }

    public function getImg ($filename)
    {
        // dd($filename);
        $path = Storage::path('template/' . $filename);
        
        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
    
        return $response;
    }

    public function post_plan_edit ($id) {
        $plan = Plan::where('id', $id)->first();
        $plan->name = request()->post('plan_name');
        $plan->description = request()->post('plan_desc');
        $plan->price = request()->post('plan_price');
        $plan->price_per_day = request()->post('price_per_day');
        $plan->total_days = request()->post('total_days');

        $plan->save();

        return redirect()->route('plan_list');
    }

    public function plan_edit ($id) {
        $plan = Plan::where('id', $id)->first();

        return view('admin.plan-edit', compact('plan', 'id'));
    }

    public function user_add (Request $request) {
        $roles = Role::whereIn('id', [1, 2, 4, 5, 6])->get();

        return view('admin.user-add', compact('roles'));
    }

    public function user_add_post (Request $request) {
        // dd($request->ssm_cert);

        $input = $request->all();
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => 'required|min:6',
            'password_confirmation' => 'required|min:6|same:password',
        ];

        $messages = [
            'password_confirmation.same' => 'Password Confirmation should match the Password',
        ];
        
        $validator = Validator::make($input, $rules, $messages);
        
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->messages());
        } else {
            $filename = $request->file('ssm_cert')->getClientOriginalName();
    
            $user = new DashboardUser;
            $user->email = $request->email;
            $user->name = $request->name;
            $user->password = Hash::make($request->password);
            $user->dob =  date('Y-m-d', strtotime($request->dob));
            $user->ssm_cert = $filename;
            $user->ssm_no =  $request->ssm_no;
            
            $user->save();

            $path = $request->file('ssm_cert')->storeAs(
                $user->id.'/ssm/', $filename
            );

            $role = Role::where('id', $request->role)->first();
            $user->assignRole($role);

            app('App\Http\Controllers\EmailController')->send_mail('New User', $user->name, $user->email, 'Welcome to Al Khairi Care', 'New User');

            return redirect()->route('user_list');
        }
        // return redirect()->route('user_list');
    }

    public function user_edit ($id) {
        $roles = Role::whereIn('id', [1, 2, 4, 5, 6])->get();
        $user = DashboardUser::where('id', $id)->first();

        return view('admin.user-edit', compact('roles', 'user', 'id'));
    }

    public function user_edit_post (Request $request) {
        // dd($request->all());
        $user = DashboardUser::where('id', $request->id)->first();
        $user->email = $request->email;
        $user->name = $request->name;
        $user->dob =  date('Y-m-d', strtotime($request->dob));
        $user->ssm_no = $request->ssm_no;
        
        $user->save();

        $roles = Role::whereIn('id', [1, 2, 4, 5])->get();
        foreach ($roles as $i => $role) {
            $user->removeRole($role);
        }
        $role = Role::where('id', $request->role)->first();
        $user->assignRole($role);

        return redirect()->route('user_list');
    }

    public function user_delete ($id) {
        $user = DashboardUser::where('id', $id)->first();
        Storage::deleteDirectory('/'.$user->id.'/ssm/');
        $user->delete();
        return redirect()->route('user_list');
    }

    public function ssm_cert_download ($id) {
        $user = DashboardUser::where('id', $id)->first();

        return Storage::download('/'.$id.'/ssm/'.$user->ssm_cert);
    }

    public function jemaah_show ($id) {
        $plans = Plan::whereIn('id', [1, 5, 6, 7])->get();
        $tpas = Plan::whereNotIn('id', [1, 5, 6, 7, 8])->get();
        $jemaah = Order::where('id', $id)->first();

        //dd($jemaah);
        return view('admin.excel-edit', compact('plans', 'tpas', 'jemaah'));
    }

    public function jemaah_edit (Request $request, $id) {
        // dd($request->all());
        $jemaah = Order::where('id', $id)->first();
        $jemaah->name = $request->name;
        $jemaah->passport_no = $request->passport_no;
        $jemaah->ic_no = $request->ic_no;
        $jemaah->dob = $request->dob;
        $jemaah->ex_illness = $request->ex_illness;
        $jemaah->hp_no = $request->hp_no;
        $jemaah->email = $request->email;
        $jemaah->dep_date = $request->dep_date;
        $jemaah->return_date = $request->return_date;
        $jemaah->plan_type = $request->plan_type;
        $jemaah->tpa = $request->tpa;
        $jemaah->pcr = $request->pcr;
        $jemaah->status = $request->jemaah_status;

        $jemaah->save();
        Session::flash('success', 'Jemaah Updated');
        return redirect()->back();
        // return view('admin.excel-edit');
    }

    public function post_edit_ta_name (Request $request) {
        $uploads = FileUpload::where('id', $request->ta_id)->first();
        $uploads->ta_name = $request->ta_name;
        $uploads->save();

        Session::flash('success', 'Travel Agent Name Updated');
        return redirect()->back();
    }

    public function post_edit_cert_no (Request $request) {
        $orders = Order::where([['file_id', $request->id], ['plan_type', '!=', 'NO']])->get();
        $year = Carbon::now()->year;
        // dd($dt);
        // $str1 = explode('A'.$year, $request->cert_no);
        $startNum = $request->cert_no - 1;
        // dd($str1[1]);
        foreach ($orders as $i => $order) {
            $str2 = explode('A'.$year, $order->ecert);
            $ecert = $startNum + ($i + 1);
            $order->ecert = 'A'.$year.$ecert;
            $order->save();
        }
        // $uploads->ta_name = $request->ta_name;
        // $uploads->save();

        Session::flash('success', 'Ecert Number Updated');
        return redirect()->back();
    }

    public function supp_doc_post_admin(Request $request)
    {
        $uploads = FileUpload::where('id', request()->post('id'))->first();
        $collection = collect($request->all());

        if ($uploads && $uploads->status == '99') {
            return response()->json([
                'isSuccess' => false,
                'Data' => 'Upload not Allowed! Excel File Already Rejected.'
            ], 200); // Status code here
        }

        $file = $collection['file'];
        $filename = $file->getClientOriginalName();

        try {
            Storage::deleteDirectory($uploads->user_id.'/supp_doc/'.$uploads->id.'/'.$collection['type']);
        }
        catch(\Exception $ex) {
            //
        }

        $path = $collection['file']->storeAs(
            $uploads->user_id.'/supp_doc/'.$uploads->id.'/'.$collection['type'], $filename
        );

        $supp_doc = ''.($uploads->supp_doc && $uploads->supp_doc!=null? $uploads->supp_doc : '');
        $file_type = ''.($collection['type'] && $collection['type']!=null? strtoupper($collection['type']) : '');

        if ($file_type=='ETICKET') {
            $supp_doc = str_replace("T", "", $supp_doc)."T";
        }
        if ($file_type=='VISA') {
            $supp_doc = str_replace("V", "", $supp_doc)."V";
        }
        if ($file_type=='PASSPORT') {
            $supp_doc = str_replace("P", "", $supp_doc)."P";
        }
        if ($file_type=='PAYRECEIPT') {
            $supp_doc = str_replace("R", "", $supp_doc)."R";
        }

        //dd($request->type, $request->id, $uploads->supp_doc, $supp_doc, $file_type);
        $uploads->supp_doc = $supp_doc;
        $uploads->save();        

        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Uploaded!'
        ], 200); // Status code here
    }

    public function supp_doc_download_admin(Request $req, $id, $type)
    {
        //dd($id, $type);
        $uploads = FileUpload::where('id', $id)->first();
        //dd($uploads);

        $directory =  '/'.$uploads->user_id.'/supp_doc/'.$uploads->id.'/'.$type;
        //dd($directory);

        $files = Storage::allFiles($directory);
        //dd($files[0], basename($files[0]));

        if (!empty($files)) {
            $files = collect(Storage::allFiles($directory));
            $ext = pathinfo($files[0], PATHINFO_EXTENSION);
            $ext = strtolower($ext);
            if ($ext == 'pdf' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'png') {
                //header('Content-disposition','attachment; filename="test"');
                return response()->file(Storage::path($files[0]), [ 'Content-Disposition' => 'filename="'.basename($files[0]).'"' ]);
    
            }
            return Storage::download($files[0]);
        }
    }
    
    public function supp_doc_check ($id, $type) {
        $uploads = FileUpload::where('id', $id)->first();
        //dd($id, $type);
        
        if ($uploads->supp_doc !== null) {
            $fileArr = array();
            $typeArr = array();
            if (str_contains($type, 'P')) {
                $folderType = 'passport';
                array_push($typeArr, $folderType);
            }
            
            if (str_contains($type, 'T')) {
                $folderType = 'eticket';
                array_push($typeArr, $folderType);
            } 
            
            if (str_contains($type, 'V')) {
                $folderType = 'visa';
                array_push($typeArr, $folderType);
            } 
            
            if (str_contains($type, 'R')) {
                $folderType = 'payreceipt';
                array_push($typeArr, $folderType);
            }

            foreach ($typeArr as $key => $arr) {
                $directory =  '/'.$uploads->user_id.'/supp_doc/'.$uploads->id.'/'.$arr;
                $files = Storage::allFiles($directory);
                if (!empty($files)) {
                    $tempArr = array(
                        $arr => basename($files[0])
                    );
                    array_push($fileArr, $tempArr);
                }
            }
            
            return response()->json([
                'isSuccess' => true,
                'Data' => $fileArr
            ], 200); // Status code here
        }
    }

    public function cancel_invoice ($id) {
        $uploads = FileUpload::where('id', $id)->first();
        $uploads->status = '2.3';
        $uploads->save();

        return redirect()->route('excel_list_admin');
    }
}
