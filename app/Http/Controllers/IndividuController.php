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

class IndividuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['role:ind|akc']);
    }

    
    public function application()
    {
        $plans = Plan::all();
        $dt = Carbon::now();
        $min_date = $dt->toDateString();

        return view('individu.application',  compact('plans', 'min_date'));
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
}
