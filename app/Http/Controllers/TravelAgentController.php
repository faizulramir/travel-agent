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

class TravelAgentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['role:tra|akc|ind']);
    }

    
    public function excel_list()
    {
        //$uploads = FileUpload::where('user_id', Auth::id())->get();
        $uploads = FileUpload::where('user_id', Auth::id())->orderBy('upload_date', 'DESC')->orderBy('status', 'ASC')->get();

        return view('travel-agent.excel-list', compact('uploads'));
    }

    public function delete_excel_ta($id)
    {
        $uploads = FileUpload::where('id', $id)->first();

        Storage::deleteDirectory($uploads->user_id.'/excel/'.$uploads->id);
        Storage::deleteDirectory($uploads->user_id.'/ecert/'.$uploads->id);
        Storage::deleteDirectory($uploads->user_id.'/supp_doc/'.$uploads->id);
        Storage::deleteDirectory($uploads->user_id.'/payment/'.$uploads->id);
        Storage::deleteDirectory($uploads->user_id.'/pcr_result/'.$uploads->id);
        $uploads->delete();

        return redirect()->back();
    }

    public function excel_detail_ta($id)
    {
        $orders = Order::where('file_id', $id)->get();
        $uploads = FileUpload::where('id', $id)->first();
        $payment = Payment::where('file_id', $id)->first();
        $check = FileUpload::where([['id', $id], ['status', '5']])->get();

        return view('travel-agent.detail-excel', compact('orders', 'uploads', 'payment', 'check',  'id'));
    }

    public function update_detail_ta($id, $status)
    {
        $orders = Order::where('id', $id)->first();
        $orders->status = $status;

        $orders->save();

        return redirect()->back();
    }


    public function excel_post_ta(Request $request)
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

    public function supp_doc_post_ta(Request $request)
    {
        $uploads = FileUpload::where('id', request()->post('id'))->first();
        $collection = collect($request->all());

        $file = $collection['file'];
        $filename = $file->getClientOriginalName();

        $path = $collection['file']->storeAs(
            //Auth::id().'/supp_doc/'.$uploads->id, $filename
            Auth::id().'/supp_doc/'.$uploads->id.'/'.$collection['type'], $filename
        );

        //$uploads->status = '1';
        //$uploads->supp_doc = '1';
        //$uploads->save();

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

    public function submit_post_ta(Request $request)
    {
        $dt = Carbon::now();

        $uploads = FileUpload::where('id', request()->post('id'))->first();
        $uploads->status = '2';
        //$uploads->submit_date = $dt->toDateString();
        $uploads->submit_date = $dt->toDateTimeString();
        $uploads->save();
        
        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Submitted!'
        ], 200); // Status code here
    }

    public function download_template()
    {
        return Storage::download('/template/AKC-ECARE-TEMPLATE-v1.0.xlsx');
    }

    public function download_cert()
    {
        return Storage::download('/cert/e-cert.pdf');
    }

    public function download_invoice()
    {
        return Storage::download('/invoice/invoice.pdf');
    }
}
