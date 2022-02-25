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
use App\Exports\ClaimExport;
use Maatwebsite\Excel\Facades\Excel;

class ClaimController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['role:fin|akc|mkh']);
    }
    
    public function claim_list()
    {
        $files = FileUpload::where('status','=','5')->get();
        $temp_file = array();

        foreach ($files as $i => $file) {
            $pcr = Order::where([['file_id', '=', $file->id], ['pcr', '=', 'PCR']])->get();
            if ($pcr->isNotEmpty()) {
                $fileUploads = FileUpload::where('id', $file->id)->first();;
                array_push($temp_file, $fileUploads);
            }
        }
        return view('claim.claim-list', compact('temp_file'));
    }

    public function claim_detail($id)
    {
        $orders = Order::where([['file_id', '=', $id]])->orderBy('pcr_result', 'DESC')->get();
        $file_id = $id;
        return view('claim.claim-detail', compact('orders', 'file_id'));
    }

    public function claim_edit($id)
    {
        $plans = Plan::whereIn('id', [1, 5, 6, 7])->get();
        $tpas = Plan::whereNotIn('id', [1, 5, 6, 7, 8])->get();
        $jemaah = Order::where('id', $id)->first();
        
        return view('claim.claim-edit', compact('plans', 'tpas', 'jemaah'));
    }

    public function get_claim_json ($id)
    {
        $jemaah = Order::where('id', $id)->first();
        $claimJson = json_decode($jemaah->claim_json, true);
        $claimJson = $claimJson['data'];

        return response()->json([
            'isSuccess' => true,
            'Data' => $claimJson,
        ], 200);
    }

    public function claim_add(Request $request)
    {
        $jemaah = Order::where('id', $request->id)->first();
        $json_data = json_decode(request()->post('jsonData'));
        $chunck_json = array_chunk($json_data, 7);
        
        $arr_json = array();
        foreach ($chunck_json as $i => $json) {
            $temp_arr = array(
                'rowInput1' => $json[0],
                'rowInput2' => $json[1],
                'rowInput3' => $json[2],
                'rowInput4' => $json[3],
                'rowInput5' => $json[4],
                'rowInput6' => $json[5],
                'rowInput7' => $json[6],
            );

            array_push($arr_json, $temp_arr);
        }

        $arr_data = array(
            'data' => $arr_json,
        );

        $jemaah->claim_json = json_encode($arr_data);
        $jemaah->save();
        
        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Submitted!'
        ], 200);
    }

    public function application_list()
    {
        $uploads = FileUpload::where('user_id', Auth::id())->get();
        return view('individu.application-list', compact('uploads'));
    }

    public function application_post_excel(Request $request)
    {
        $arr_json = request()->post('json_post');

        $dt = Carbon::now();
        // dd(request()->post('travel_agent'))
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

        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Uploaded!'
        ], 200); // Status code here
    }

    public function application_detail($id)
    {
        $orders = Order::where('file_id', $id)->get();
        $uploads = FileUpload::where('id', $id)->first();
        $payment = Payment::where('file_id', $id)->first();

        return view('individu.application-detail', compact('orders', 'uploads', 'payment'));
    }

    public function submit_post_ind()
    {
        $dt = Carbon::now();

        $uploads = FileUpload::where('id', request()->post('id'))->first();
        $uploads->status = '2';
        $uploads->submit_date = $dt->toDateString();
        $uploads->save();

        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Submitted!'
        ], 200); // Status code here
    }

    public function application_post(Request $request)
    {
        $dt = Carbon::now();

        $uploads = new FileUpload;
        $uploads->upload_date = $dt->toDateString();
        $uploads->status = '0';
        $uploads->supp_doc = '1';
        $uploads->ta_name = request()->post('travel_agent');
        $uploads->user_id = Auth::id();

        $uploads->save();

        $file_passport = $request->file('passport_file');
        $file_visa = $request->file('visa_file');
        $file_ticket = $request->file('ticket_file');

        $filename_passport = $file_passport->getClientOriginalName();
        $filename_visa = $file_visa->getClientOriginalName();
        $filename_ticket = $file_ticket->getClientOriginalName();

        $request->passport_file->storeAs(
            Auth::id().'/supp_doc/'.$uploads->id, $filename_passport
        );
        $request->visa_file->storeAs(
            Auth::id().'/supp_doc/'.$uploads->id, $filename_visa
        );
        $request->ticket_file->storeAs(
            Auth::id().'/supp_doc/'.$uploads->id, $filename_ticket
        );
        
        $plans = Plan::where('id', request()->post('plan'))->first();

        $order = new Order;
        $order->name = request()->post('name');
        $order->passport_no = request()->post('passport_no');
        $order->ic_no = request()->post('ic_no');
        $order->dob = request()->post('dob');
        $order->ex_illness = request()->post('ex_ill');
        $order->hp_no = request()->post('phone_no');
        $order->plan_type = $plans->name;
        $order->email = request()->post('email');
        $order->dep_date = request()->post('dep_date');
        $order->return_date = request()->post('return_date');
        $order->user_id = Auth::id();
        $order->file_id = $uploads->id;

        $order->save();

        $dt = Carbon::now();
        $orderdate = $dt->toDateString();
        $orderdate = explode('-', $orderdate);
        $year  = $orderdate[0];

        $orders = Order::where('id', '=' ,$order->id)->first();
        $orders->ecert = 'A'.$year.$orders->file_id.$orders->id;
        $orders->invoice = 'I'.$year.$orders->file_id.$orders->id;

        $orders->save();

        return redirect()->route('application_list');;
    }

    public function application_delete($id)
    {
        $uploads = FileUpload::where('id', $id)->first();
        Storage::deleteDirectory(Auth::id().'/supp_doc/'.$uploads->id);
        Storage::deleteDirectory(Auth::id().'/payment/'.$uploads->id);
        $uploads->delete();

        return redirect()->back();
    }

    public function supp_doc_post_ind(Request $request)
    {
        $uploads = FileUpload::where('id', request()->post('id'))->first();

        $collection = collect($request->all());

        $file = $collection['file'];
        $filename = $file->getClientOriginalName();

        $path = $collection['file']->storeAs(
            Auth::id().'/supp_doc/'.$uploads->id, $filename
        );

        // $uploads->status = '1';
        $uploads->supp_doc = '1';
        $uploads->save();

        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Uploaded!'
        ], 200); // Status code here
    }

    public function  export_claim (Request $request, $id)
    {
        $headers[] = [
            'Date',
            'Company ID',
            'Pt. File#',
            'Patient Name',
            'Invoice#',
            'Consultation',
            'Drugs',
            'Services',
            'Grand Total',
            'Discount',
            'Total',
        ];

        $json_arr = array();
        // <td><div class="form-group"><input class="form-control" name="rowInput1" placeholder="Enter Date" type="date" value="${e.rowInput1}"></div></td>
        // <td><div class="form-group"><input class="form-control" name="rowInput2" placeholder="Enter Pt. File#" type="number" value="${e.rowInput2}"></div></td>
        // <td><div class="form-group"><input class="form-control" name="rowInput3" placeholder="Enter Invoice#" type="number" value="${e.rowInput3}"></div></td>
        // <td><div class="form-group"><input class="form-control" name="rowInput4" placeholder="Enter Consultation" type="number" value="${e.rowInput4}"></div></td>
        // <td><div class="form-group"><input class="form-control" name="rowInput5" placeholder="Enter Drugs" type="number" value="${e.rowInput5}"></div></td>
        // <td><div class="form-group"><input class="form-control" name="rowInput6" placeholder="Enter Services" type="number" value="${e.rowInput6}"></div></td>
        // <td><div class="form-group"><input class="form-control" name="rowInput7" placeholder="Enter Other" type="number" value="${e.rowInput7}">
        $orders = Order::where([['file_id', '=', $id], ['claim_json', '!=', null]])->get();
        $file_name = FileUpload::where('id', $id)->first();
        foreach ($orders as $i => $order) {
            $json_d = json_decode($order->claim_json, true);
            $json_d = $json_d['data'];
            foreach ($json_d as $j => $json) {
                $g_total = ($json['rowInput4'] ? $json['rowInput4'] : 0) + ($json['rowInput5'] ? $json['rowInput5'] : 0) + ($json['rowInput6'] ? $json['rowInput6'] : 0);
                $total = $g_total - ($json['rowInput7'] ? $json['rowInput7'] : 0);
                $temp_arr = array(
                    $json['rowInput1'],
                    $order->ecert,
                    $json['rowInput2'],
                    $order->name,
                    $json['rowInput3'],
                    $json['rowInput4'],
                    $json['rowInput5'],
                    $json['rowInput6'],
                    $g_total,
                    $json['rowInput7'],
                    $total,
                );
                array_push($json_arr, $temp_arr);
            }
        }
        // dd($json_arr);
        $export = new ClaimExport([
            $json_arr
        ]);

        return Excel::download($export, 'claims_'.$file_name->ta_name.'.xlsx');
    }
}


