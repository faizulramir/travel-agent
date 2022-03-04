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

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['role:tra|akc|ag|fin|mkh|ind']);
    }

    
    public function get_notification () {
        $upload = DB::select(
            DB::raw("SELECT * FROM file_upload WHERE updated_at >= NOW() - INTERVAL 5 MINUTE")
        );
        // dd($upload);
        return response()->json([
            'isSuccess' => true,
            'Data' => $upload
        ], 200);
    }

}
