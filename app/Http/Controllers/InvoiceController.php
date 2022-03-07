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
        dd($json_data);

        if ($json_data) {
            $inv_no = strtoupper($json_data->inv_no);
            $inv_date = $json_data->inv_date;
            $inv_company = strtoupper($json_data->inv_company);
            $inv_remark = strtoupper($json_data->inv_remark);
            $inv_status = strtoupper($json_data->inv_status);
            $inv_total = $json_data->inv_total;
            $inv_showtotal = $json_data->inv_showtotal;
            $inv_entries = $json_data->entries;
        }
        else {
            return response()->json([
                'isSuccess' => false,
                'Data' => 'Data Incorrect. Failed to save Invoice!'
            ], 200);
        }

        //dd($inv_no, $inv_date, $inv_company, $inv_remark, $inv_total, $entries);

        $invoice = new Invoice;
        $invoice->inv_no = $inv_no;
        $invoice->$inv_date = $inv_date;
        $invoice->$inv_company = $inv_company;
        $invoice->inv_remark = $inv_remark;
        $invoice->inv_status = $inv_status;
        $invoice->inv_showtotal = $inv_showtotal;
        //$invoice->json_inv = $inv_entries;
        
        $invoice->save();

        return response()->json([
            'isSuccess' => true,
            'Data' => 'Successfully Submitted!'
        ], 200);
    }


    
}


