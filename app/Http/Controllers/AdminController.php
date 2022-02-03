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

class AdminController extends Controller
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

    public function admin_payment_detail($id)
    {
        $uploads = FileUpload::where('id', $id)->first();
        $pay = Payment::where('file_id', $id)->first();

        $tot_rec = 0;
        $orders = Order::where([['file_id', '=' ,$id],['status', '1']])->get();
        $tot_rec = count($orders);

        //all plans
        $plans = Plan::all();

        //prepare E-CARE plan group
        $plan_arr = array();
        $costing_arr = array();

        $tot_ecert = 0.00;
        $tot_pcr = 0.00;
        $tot_tpa = 0.00;
        

        foreach ($orders as $order) {

            if ($order->plan_type!=null && $order->plan_type!='' && $order->plan_type!='NO') {
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

                //calculate for PCR
                $pcr = 0.00;   //pcr price



                //calculate for TPA
                $tpa = 0.00;   //tpa price



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
                    'TPA' => $tpa,
                );
                array_push($costing_arr, $tmpArr); //prepare costing for each record
                array_push($plan_arr, $order->plan_type);   //grouping the selected plans
            }

            //echo $order;
            //array_push($plan_arr,  $order->plan_type);
        }
        // print_r($costing_arr);

        //group ECERT plan
        //$plan_arr = \Arr::where($plan_arr, function ($value, $key) {
        //    return $value!=null && $value!='' && $value!='NO';
        //});
        $plan_arr = array_count_values($plan_arr);  //count plan grouping
        //print_r($plan_arr);

        $invoice_arr = array();
        foreach ($plan_arr as $plan => $tot_count) {
            echo $plan .' === '. $tot_count ."<br>";
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

        $tot_inv = $tot_ecert + $tot_pcr + $tot_tpa;

        //die();
        return view('admin.invoice', compact('uploads', 'pay', 'plan_arr', 'plans', 'invoice_arr', 'tot_inv', 'tot_rec'));
    }

    public function excel_list_admin()
    {
        $uploads = FileUpload::all();
        $users = DashboardUser::all();
        return view('admin.excel-list', compact('uploads', 'users'));
    }

    public function excel_detail_admin($id)
    {
        $orders = Order::where('file_id', $id)->get();
        $uploads = FileUpload::where('id', $id)->first();
        $payment = Payment::where('file_id', $id)->first();

        return view('admin.detail-excel', compact('orders', 'uploads', 'payment'));
    }

    public function excel_post_admin(Request $request)
    {
        $arr_json = request()->post('json_post');

        $dt = Carbon::now();
        
        $uploads = new FileUpload;
        $uploads->file_name = request()->post('file_name');
        $uploads->upload_date = $dt->toDateString();
        $uploads->status = '3';
        $uploads->ta_name = request()->post('travel_agent');
        $uploads->user_id = request()->post('user');
        $uploads->submit_date = $dt->toDateString();
        $uploads->save();

        $collection = collect($request->all());

        $file = $collection['file'];
        $filename = $file->getClientOriginalName();

        $path = $collection['file']->storeAs(
            request()->post('user').'/excel/'.$uploads->id, $filename
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
        $year  = $orderdate[0];

        // dd($data_array);
        foreach ($data_array as $i => $json) {
            $order = new Order;
            $order->name = $json[1];
            $order->passport_no = $json[2];
            $order->ic_no = $json[3];
            $order->dob = $json[4];
            $order->ex_illness = $json[5];
            $order->hp_no = $json[6];
            $order->plan_type = $json[7];
            $order->email = $json[8];
            $order->dep_date = $json[9];
            $order->return_date = $json[10];
            $order->user_id = Auth::id();
            $order->file_id = $uploads->id;
            $order->ecert = $uploads->id;
            $order->invoice = $uploads->id;

            $order->pcr = $json[11];
            $order->tpa = $json[12];

            $order->save();

            $orders = Order::where('id', '=' ,$order->id)->first();
            $orders->ecert = 'A'.$year.$orders->file_id.$orders->id;
            $orders->invoice = 'I'.$year.$orders->file_id.$orders->id;
            $orders->save();
        }
        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Uploaded!'
        ], 200); // Status code here
    }

    public function update_excel_status_admin($id, $status)
    {
        $uploads = FileUpload::where('id', $id)->first();
        $uploads->status = $status;
        $uploads->save();

        if ($uploads->file_name) {
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
            $year  = $orderdate[0];

            // dd($data_array);
            foreach ($data_array as $i => $json) {
                $order = new Order;
                $order->name = $json[1];
                $order->passport_no = $json[2];
                $order->ic_no = $json[3];
                $order->dob = $json[4];
                $order->ex_illness = $json[5];
                $order->hp_no = $json[6];
                $order->plan_type = $json[7];
                $order->email = $json[8];
                $order->dep_date = $json[9];
                $order->return_date = $json[10];
                $order->user_id = Auth::id();
                $order->file_id = $uploads->id;
                $order->ecert = $uploads->id;
                $order->invoice = $uploads->id;

                $order->pcr = $json[11];
                $order->tpa = $json[12];

                $order->save();

                $orders = Order::where('id', '=' ,$order->id)->first();
                $orders->ecert = 'A'.$year.$orders->file_id.$orders->id;
                $orders->invoice = 'I'.$year.$orders->file_id.$orders->id;
                $orders->save();
            }
        }

        return redirect()->back();
    }

    public function user_list()
    {
        // $role = Role::create(['name' => 'fin']);
        // $role = Role::where('id', 3)->first();
        // Auth::user()->assignRole($role);
        $roles = Role::whereIn('id', [1, 2, 4, 5])->get();
        $users = DashboardUser::all();
        return view('admin.user-list', compact('users',  'roles'));
    }

    public function plan_list()
    {
        $plans = Plan::all();

        return view('admin.plan', compact('plans'));
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
    
}
