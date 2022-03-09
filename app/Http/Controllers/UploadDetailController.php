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

class UploadDetailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['role:akc|fin|tra|ag|ind']);
    }

    public function upload_detail($id)
    {
        $uploads = FileUpload::where('id', $id)->first();

        /*
        $url = Storage::path($uploads->user_id.'/excel/'.$uploads->id.'/'.$uploads->file_name);
        $inputFileName = $url;

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($url);
        $spreadsheet = $spreadsheet->getActiveSheet();
        //$spreadsheet = $spreadsheet->getFirstSheetIndex();
        $orders =  $spreadsheet->toArray();
        //dd($orders);

        //remove excel header entry
        unset($orders[0]);
        unset($orders[9]);
        //filter only available entry - checking row number availability
        $orders = \Arr::where($orders, function ($value, $key) {
            return $value[0]!=null && $value[0]!='';
        });
        //print_r($orders);
        //die();
        */

        $new_orders = array();

        //get excel file
        if (auth()->user()->hasAnyRole('ind') && !$uploads->file_name) {
            $orders = Order::where('file_id', $uploads->id)->get();
            foreach ($orders as $i => $order) {
                // dd($order);
                $tmp_arr = array(
                    $i,
                    $order->name, 
                    $order->passport_no, 
                    $order->ic_no, 
                    date(strtotime($order->dob)), 
                    $order->ex_illness, 
                    $order->hp_no, 
                    $order->plan_type, 
                    $order->email, 
                    date(strtotime($order->dep_date)), 
                    date(strtotime($order->return_date)), 
                    $order->pcr, 
                    $order->tpa
                );
            }
            
            array_push($new_orders, $tmp_arr);

            $orders = $new_orders;
            // dd($orders);
            return view('upload.detail', compact('orders'));
        }

        $url = Storage::path($uploads->user_id.'/excel/'.$uploads->id.'/'.$uploads->file_name);
        
        $inputFileName = $url;

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
        $orders = $ssheet->toArray(); 
        //dd($orders);

        //remove non-data and header rows
        unset($orders[0]);
        unset($orders[9]);
        //dd($data_array);

        //filter only available entry - checking row number availability
        $orders = \Arr::where($orders, function ($value, $key) {
            return $value[0]!=null && $value[0]!='';
        });
        //print_r($data_array);
        //die();
        //dd($orders);
        
        //date checking errors
        $error_sts = false;
        $error_msg = '';

        
        
        if ($orders) {
            //process data jemaah
            foreach ($orders as $i => $order) {
                //dd($order);
                
                $dob_date = 0;
                $dep_date = 0;
                $rtn_date = 0;

                //dob date
                try {
                    $dob_date = ''.date('d-m-Y',\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($order[4]));
                    //dd("try4", $order[4], $dob_date);
                } catch (\Throwable $th) {
                    $dob_date = ''.$order[4];
                    //dd("catch4", $order[4], $dob_date);
                }     


                //dep date
                try {
                    $dep_date = strtotime(''.date('d-m-Y',\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($order[9])));
                    $order[9] = ''.date('d-m-Y',\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($order[9]));
                    //dd("try9", $order[9], date('d-m-Y', $order[9]));
                } catch (\Throwable $th) {
                    //dd("catch9", $order[9]);

                    $tmp_date = $order[9];
                    $tmp_date = trim(str_replace('/', '-', $tmp_date));
                    $explode = explode('-', $tmp_date);

                    if (count($explode)==3) {
                        if ((int)$explode[1]>0 && (int)$explode[1]<13 && (int)$explode[2]>2000) {
                            $tmp_date = ''. (int)$explode[0] .'-'. (int)$explode[1] .'-'. (int)$explode[2];
                            //dd($tmp_date);
                            $new_date = Carbon::createFromFormat('d-m-Y',  $tmp_date)->format('d-m-Y');
                            $new_date = strtotime($new_date);
                            
                            if ($new_date<0) {
                                //$error_sts = true;
                                //$error_msg = 'DEP Date Incorrect Format. Expected dd-mm-yyyy format. Approve Process Failed!';
                            }
                            else {
                                $order[9] = date('d-m-Y', $new_date);
                                $dep_date = strtotime(date('d-m-Y', $new_date));
                            }
                        }
                        else {
                            //$error_sts = true;
                            //$error_msg = 'DEP Date Incorrect Format. Expected dd-mm-yyyy format. Approve Process Failed!';
                        }
                    }
                    else {
                        //$error_sts = true;
                        //$error_msg = 'DEP Date Incorrect Format. Expected dd-mm-yyyy format. Approve Process Failed!';
                    }
                    //dd("catch9", $order[9], $tmp_date, $explode, $error_sts, $error_msg);
                }

                //rtn date
                try {
                    $rtn_date = strtotime(''.date('d-m-Y',\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($order[10])));
                    $order[10] = ''.date('d-m-Y',\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($order[10]));
                    //dd("try10", $order[10]);
                } catch (\Throwable $th) {
                    //dd("catch10", $order[10]);

                    $tmp_date = $order[10];
                    $tmp_date = trim(str_replace('/', '-', $tmp_date));
                    $explode = explode('-', $tmp_date);

                    if (count($explode)==3) {
                        if ((int)$explode[1]>0 && (int)$explode[1]<13 && (int)$explode[2]>2000) {
                            $tmp_date = ''. (int)$explode[0] .'-'. (int)$explode[1] .'-'. (int)$explode[2];
                            //dd($tmp_date);
                            $new_date = Carbon::createFromFormat('d-m-Y',  $tmp_date)->format('d-m-Y');
                            $new_date = strtotime($new_date);
                            
                            if ($new_date<0) {
                                //$error_sts = true;
                                //$error_msg = 'DEP Date Incorrect Format. Expected dd-mm-yyyy format. Approve Process Failed!';
                            }
                            else {
                                $order[10] = date('d-m-Y', $new_date);
                                $rtn_date = strtotime(date('d-m-Y', $new_date));
                            }
                        }
                        else {
                            //$error_sts = true;
                            //$error_msg = 'DEP Date Incorrect Format. Expected dd-mm-yyyy format. Approve Process Failed!';
                        }
                    }
                    else {
                        //$error_sts = true;
                        //$error_msg = 'DEP Date Incorrect Format. Expected dd-mm-yyyy format. Approve Process Failed!';
                    }
                    //dd("catch10", $order[10], $tmp_date, $explode, $error_sts, $error_msg);
                }

                $tmp_arr = array($order[0], $order[1], $order[2], $order[3], $dob_date, $order[5], $order[6], $order[7], $order[8], $dep_date, $rtn_date, $order[11], $order[12]);
                array_push($new_orders, $tmp_arr);
                //dd($tmp_arr, $new_orders);
            }
        }

        $orders = $new_orders;
        //dd($orders);

        return view('upload.detail', compact('orders'));
    }

}
