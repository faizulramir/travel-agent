<?php
    
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Session;
use Stripe;
use App\Models\DashboardUser;
use App\Models\FileUpload;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class PcrController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */

    public function pcr_excel_list()
    {
        //$files = FileUpload::all();
        $files = FileUpload::where('status','=','5')->orderBy('submit_date', 'DESC')->orderBy('status', 'ASC')->get();

        $temp_file = array();

        foreach ($files as $i => $file) {
            $pcr = Order::where([['file_id', '=', $file->id], ['pcr', '=', 'PCR']])->get();
            if ($pcr->isNotEmpty()) {
                $fileUploads = FileUpload::where('id', $file->id)->first();;
                array_push($temp_file, $fileUploads);
            }
        }
        
        return view('pcr.excel-list', compact('temp_file'));
    }

    public function excel_detail_pcr($id)
    {
        $orders = Order::where([['file_id', '=', $id], ['pcr', '=', 'PCR']])->get();

        return view('pcr.pcr-list', compact('orders'));
    }

    public function post_return_date($val, $id)
    {
        $order = Order::where('id', $id)->first();
        $order->pcr_date = $val;
        $order->save();

        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Updated'
        ], 200);
    }

    public function post_pcr_doc(Request $request)
    {
        $collection = collect($request->all());
        $file = $collection['file'];
        $filename = $file->getClientOriginalName();

        $order = Order::where('id', request()->post('id'))->first();

        if ($order->pcr_file_name) {
            Storage::deleteDirectory('/'.$order->user_id.'/pcr_result/'.$order->id);
        }
        // $order->pcr_result = '1';
        $order->pcr_file_name = $filename;
        $order->save();

        $path = $collection['file']->storeAs(
            $order->user_id.'/pcr_result/'.$order->id, $filename
        );
        // $order = Order::where('id', $id)->first();
        // $order->return_date = $val;
        // $order->save();

        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Updated'
        ], 200);
    }

    public function downloadPCR($user_id, $order_id, $file_name)
    {
        return Storage::download('/'.$user_id.'/pcr_result/'.$order_id.'/'.$file_name);
    }

    public function post_quarantine(Request $request)
    {
        // dd($request->all());
        $order = Order::where('id', $request->id)->first();
        $order->pcr_result = $request->pcr_result;
        $order->save();

        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Updated'
        ], 200);
    }
}