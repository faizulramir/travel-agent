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

        //dd($orders);

        return view('upload.detail', compact('orders'));
    }

}
