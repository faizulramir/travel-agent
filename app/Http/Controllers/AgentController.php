<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FileUpload;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AgentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['role:ag|akc']);
    }

    
    public function excel_list_agent()
    {
        $uploads = FileUpload::where('user_id', Auth::id())->orderBy('status', 'ASC')->orderBy('submit_date', 'DESC')->get();
        return view('agent.excel-list', compact('uploads'));
    }

    public function delete_excel_agent($id)
    {
        $uploads = FileUpload::where('id', $id)->first();
        Storage::deleteDirectory(Auth::id().'/excel/'.$uploads->id);
        Storage::deleteDirectory(Auth::id().'/supp_doc/'.$uploads->id);
        Storage::deleteDirectory(Auth::id().'/payment/'.$uploads->id);
        $uploads->delete();

        return redirect()->back();
    }

    public function update_detail_agent($id, $status)
    {
        $orders = Order::where('id', $id)->first();
        $orders->status = $status;

        $orders->save();

        return redirect()->back();
    }

    public function excel_detail_agent($id)
    {
        $orders = Order::where('file_id', $id)->get();
        $uploads = FileUpload::where('id', $id)->first();
        $payment = Payment::where('file_id', $id)->first();
        $check = FileUpload::where([['id', $id], ['status', '5']])->get();
        
        return view('agent.detail-excel', compact('orders', 'uploads', 'payment', 'check'));
    }

    public function excel_post_agent(Request $request)
    {
        $arr_json = request()->post('json_post');

        $dt = Carbon::now();
        
        $uploads = new FileUpload;
        $uploads->file_name = request()->post('file_name');
        $uploads->upload_date = $dt->toDateString();
        $uploads->status = '0';
        $uploads->ta_name = request()->post('travel_agent');
        $uploads->user_id = Auth::id();
        
        $uploads->save();

        $collection = collect($request->all());

        $file = $collection['file'];
        $filename = $file->getClientOriginalName();

        $path = $collection['file']->storeAs(
            Auth::id().'/excel/'.$uploads->id, $filename
        );
        
        // $json_dcd = json_decode($arr_json, true);
        // foreach ($json_dcd as $i => $json) {
        //     $dep_date = ($json['DEP DATE '] - 25569) * 86400;
        //     $return_date = ($json['RETURN DATE '] - 25569) * 86400;
        //     $dob = ($json['DATE OF BIRTH  '] - 25569) * 86400;
            
        //     $order = new Order;
        //     $order->name = $json['NAME '];
        //     $order->passport_no = $json['PASSPORT NO  '];
        //     $order->ic_no = $json['IDENTITY CARD NO '];
        //     $order->dob = gmdate("d-m-Y", $dob);
        //     $order->ex_illness = $json['EXISTING ILLNESS '];
        //     $order->hp_no = $json['HP NO '];
        //     $order->plan_type = $json['PLAN TYPE '];
        //     $order->email = $json['EMAIL ADD '];
        //     $order->dep_date = gmdate("d-m-Y", $dep_date);
        //     $order->return_date = gmdate("d-m-Y", $return_date);
        //     $order->user_id = Auth::id();
        //     $order->file_id = $uploads->id;

        //     $order->save();
        // }
        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Uploaded!'
        ], 200); // Status code here
    }

    public function supp_doc_post_agn(Request $request) {
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
            //Auth::id().'/supp_doc/'.$uploads->id, $filename
            Auth::id().'/supp_doc/'.$uploads->id.'/'.$collection['type'], $filename
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

    public function submit_post_agent(Request $request)
    {
        $dt = Carbon::now();
        $uploads = FileUpload::where('id', request()->post('id'))->first();

        //get excel file
        $url = Storage::path($uploads->user_id.'/excel/'.$uploads->id.'/'.$uploads->file_name);
        $inputFileName = $url;

        //$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($url);
        //$spreadsheet = $spreadsheet->getActiveSheet();
        //$orders =  $spreadsheet->toArray();

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

        //remove excel header entry
        unset($orders[0]);
        unset($orders[9]);
        //filter only available entry - checking row number availability
        $orders = \Arr::where($orders, function ($value, $key) {
            return $value[0]!=null && $value[0]!='';
        });
        //dd($orders);

        //date checking errors
        $error_sts = false;
        $error_msg = '';

        /*
        if ($orders) {
            foreach ($orders as $i => $order) {
                if (!$errFormat && $order[9]) {
                    $tmp_date = str_replace('/', '-', ''.$order[9]);
                    //dd($order[9], $tmp_date);

                    $explode = explode("-",$tmp_date);
                    if (count($explode)==3) {
                        //dd($explode);
                        $explode[0] = str_pad($explode[0], 2, "0", STR_PAD_LEFT);
                        $explode[1] = str_pad($explode[1], 2, "0", STR_PAD_LEFT);
                        $tmp_date = implode('-', $explode);
                        //dd($tmp_date);
                    }
        
                    $test = Carbon::hasFormatWithModifiers($tmp_date, 'd#m#Y!');
                    if ($test) {
                        //echo ($tmp_date ? date('d-m-Y', strtotime($tmp_date)) : ''); //Date:OK
                    } else {
                        $test = Carbon::hasFormatWithModifiers($tmp_date, 'Y#m#d!');
                        if ($test) {
                            //echo ($tmp_date ? date('d-m-Y', strtotime($tmp_date)) : ''); //Date:OK
                        } else {
                            $test = Carbon::hasFormatWithModifiers($tmp_date, 'm#d#Y!');
                            if ($test) {
                                //echo ($tmp_date ? date('d-m-Y', strtotime($tmp_date)) : ''); //Date:OK
                            } else {
                                $errDate = 'Error DEP Date - Incorrect Date Format: '.$order[9]. ".\nExpected format: dd-mm-yyyy.\n\nSubmission Failed! ";
                                $errFormat = true;
                            }
                        }
                    }
                }

                if (!$errFormat && $order[10]) {
                    $tmp_date = str_replace('/', '-', ''.$order[10]);
                    //dd($order[9], $tmp_date);

                    $explode = explode("-",$tmp_date);
                    if (count($explode)==3) {
                        //dd($explode);
                        $explode[0] = str_pad($explode[0], 2, "0", STR_PAD_LEFT);
                        $explode[1] = str_pad($explode[1], 2, "0", STR_PAD_LEFT);
                        $tmp_date = implode('-', $explode);
                        //dd($tmp_date);
                    }                    
        
                    $test = Carbon::hasFormatWithModifiers($tmp_date, 'd#m#Y!');
                    if ($test) {
                        //echo ($tmp_date ? date('d-m-Y', strtotime($tmp_date)) : ''); //Date:OK
                    } else {
                        $test = Carbon::hasFormatWithModifiers($tmp_date, 'Y#m#d!');
                        if ($test) {
                            //echo ($tmp_date ? date('d-m-Y', strtotime($tmp_date)) : ''); //Date:OK
                        } else {
                            $test = Carbon::hasFormatWithModifiers($tmp_date, 'm#d#Y!');
                            if ($test) {
                                //echo ($tmp_date ? date('d-m-Y', strtotime($tmp_date)) : ''); //Date:OK
                            } else {
                                $errDate = 'Error RTN Date - Incorrect Date Format: '.$order[10]. ".\nExpected format: dd-mm-yyyy.\n\nSubmission Failed! ";
                                $errFormat = true;
                            }
                        }
                    }
                }

            }
        }
        */

        //validates excel data
        if ($orders) {
            foreach ($orders as $i => $order) {
                //dep date
                try {
                    $check_date = ''.date('d-m-Y',\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($order[9]));
                    //dd("try", $check_date);
                } catch (\Throwable $th) {
                    $check_date = $order[9];
                    //dd("catch", $order->dep_date);

                    $tmp_date = $order[9];
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
                                $error_msg = 'DEP Date Incorrect Format. Expected dd-mm-yyyy format. Submmission Process Failed!';
                            }
                            else {
                                $check_date = date('d-m-Y', $new_date);
                            }
                        }
                        else {
                            $error_sts = true;
                            $error_msg = 'DEP Date Incorrect Format. Expected dd-mm-yyyy format. Submmission Process Failed!';
                        }
                    }
                    else {
                        $error_sts = true;
                        $error_msg = 'DEP Date Incorrect Format. Expected dd-mm-yyyy format. Submmission Process Failed!';
                    }
                    //dd("catch", $ccheck_date, $tmp_date, $explode, $error_sts, $error_msg);
                }

                //rtn date
                try {
                    $check_date = ''.date('d-m-Y',\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($order[10]));
                    //dd("try", $check_date);
                } catch (\Throwable $th) {
                    $check_date = $order[10];
                    //dd("catch", $order->dep_date);

                    $tmp_date = $order[10];
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
                                $error_msg = 'RTN Date Incorrect Format. Expected dd-mm-yyyy format. Submmission Process Failed! 1';
                            }
                            else {
                                $check_date = date('d-m-Y', $new_date);
                            }
                        }
                        else {
                            $error_sts = true;
                            $error_msg = 'RTN Date Incorrect Format. Expected dd-mm-yyyy format. Submmission Process Failed! 2';
                        }
                    }
                    else {
                        $error_sts = true;
                        $error_msg = 'RTN Date Incorrect Format. Expected dd-mm-yyyy format. Submmission Process Failed! 3';
                    }
                    //dd("catch", $ccheck_date, $tmp_date, $explode, $error_sts, $error_msg);
                }   
            }
        }  
        
        //if error, stop and abort
        if ($error_sts) {
            Session::flash('error', $error_msg);
            return response()->json([
                'isSuccess' => false,
                'Data' => $error_msg,
            ], 200); // Status code here
        }


        $uploads->status = '2';
        $uploads->submit_date = $dt->toDateTimeString();
        $uploads->save();
        
        Session::flash('success', 'Excel Successfully Submitted.');
        return response()->json([
            'isSuccess' => true,
            'Data' => 'Excel Successfully Submitted!'
        ], 200); // Status code here
    }

    public function download_template_agn()
    {
        return Storage::download('/template/AKC-ECARE-TEMPLATE-v1.0.xlsx');
    }

    public function download_cert_agent()
    {
        return Storage::download('/cert/e-cert.pdf');
    }

    public function download_invoice_agent()
    {
        return Storage::download('/invoice/invoice.pdf');
    }

    public function supp_doc_check_agn ($id, $type) {
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

    public function supp_doc_download_agn(Request $req, $id, $type) {
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

}
