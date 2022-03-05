<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FileUpload;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Models\DashboardUser;
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
        $user = DashboardUser::where('id', Auth::user()->id)->first();
        $updated_at = $user->updated_at;
        //dd($updated_at);
        $start  = new Carbon($updated_at);
        $end = Carbon::now();
        //dd($start, $end);

        $diffmin = $start->diffInMinutes($end);
        $diffsec = $start->diffInSeconds($end);
        //dd($start, $end, $diffmin, $diffsec);

        if ($diffsec<60) {
            //if just login, select first 10 rows of latest updates
            $upload = DB::select(
                DB::raw("SELECT * FROM file_upload WHERE status in ('2', '2.1', '2.2', '2.3', '3', '4') ORDER BY updated_at DESC LIMIT 10")
            );
        } else {
            //if normal activity, select record updates within 5min
            $upload = DB::select(
                DB::raw("SELECT * FROM file_upload WHERE status in ('2', '2.1', '2.2', '2.3', '3', '4') and updated_at >= NOW() - INTERVAL 5 MINUTE")
            );
        }

        // dd($upload);
        return response()->json([
            'isSuccess' => true,
            'Data' => $upload
        ], 200);
    }

}
