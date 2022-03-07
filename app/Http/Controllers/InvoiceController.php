<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FileUpload;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Invoice;
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

class InvoiceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['role:fin|akc']);
    }
    
    public function invoice_list()
    {
        $files = Invoice::where('status','=','1')->get();
        return view('invoice.invoice-list', compact('files'));
    }

    public function invoice_add()
    {
        //$files = Invoice::where('status','=','1')->get();
        return view('invoice.invoice-add');
    }

    public function invoice_save(Request $request)
    {
        $json_data = json_decode(request()->post('jsonData'));

        if ($json_data) {
            $invoice = new Invoice;
            $invoice->inv_no = strtoupper($json_data->inv_no);
            $invoice->inv_date = $json_data->inv_date;
            $invoice->inv_company = strtoupper($json_data->inv_company);
            $invoice->inv_remark = strtoupper($json_data->inv_remark);
            $invoice->status = strtoupper($json_data->inv_status);
            $invoice->inv_total = $json_data->inv_total;
            $invoice->inv_showtotal = $json_data->inv_showtotal;
            $invoice->json_inv = json_encode($json_data->entries, true);

            $invoice->save();
    
            return response()->json([
                'isSuccess' => true,
                'Data' => 'Successfully Submitted!'
            ], 200);
        } else {
            return response()->json([
                'isSuccess' => false,
                'Data' => 'Data Incorrect. Failed to save Invoice!'
            ], 200);
        }

        //dd($inv_no, $inv_date, $inv_company, $inv_remark, $inv_total, $entries);
    }


    
}


