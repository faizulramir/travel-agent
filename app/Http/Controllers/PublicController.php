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
use DB;
use Dompdf\Dompdf;
use PDF;
use Illuminate\Support\Facades\App;


class PublicController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */

    public function search_cert_public()
    {
        return view('usersPublic.ecert-search');
    }

    public function post_cert_public(Request $request)
    {
        $months = array( 'Jan', 'Feb', 'Mar', 'Apr', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        $depdate = explode('-', $request->depart_date);

        $tmpy = $depdate[0];
        $tmpy = substr($tmpy,2);
        $tmpm = $depdate[1];
        if ($tmpm[0]=='0') $tmpm = $tmpm[1];
        $tmpd = $depdate[2];
        if ($tmpd[0]=='0') $tmpd = $tmpd[1];

        $curm = 0 + $tmpm;
        $mon = $months[$curm - 1];
        $tmpdate = $tmpd .'-'. $mon .'-'. $tmpy;
        
        //dd($request->passport, $request->depart_date, $depdate, $tmpy, $tmpm, $tmpd, $curm, $mon, $tmpdate);

        //$order = Order::where([['passport_no', '=', $request->passport], ['dep_date', '=', $request->depart_date]])->first();
        $order = Order::where([['passport_no', '=', $request->passport], ['dep_date', '=', $request->depart_date]])
                        ->orWhere([['passport_no', '=', $request->passport], ['dep_date', '=', $tmpdate], ['plan_type', '!=', 'NO']])
                        ->first();

        if ($order && $order->plan_type == 'NO') $order = null;

        if ($order == null) {
            return redirect()->back()->with(['error' => 'Jemaah not found / no ECARE plan application']);
        } else {
            Session::flash('success', 'Jemaah Found');
            Session::flash('order_id', $order->id);
            return redirect()->back()->with(['ecert' => $order->ecert, 'name' => $order->name, 'passport' => $order->passport_no]);
        }
    }

    public function download_cert_public ($order_id) {
        $orders = Order::where([['id', '=' ,$order_id],['status', '1']])->first();
        $plan = Plan::where('name', $orders->plan_type)->first();
        $url_bg = Storage::path('template/template_cert.png');

        $cert_number = $orders->ecert;

        $dob = new Carbon($orders->dob);
        $dobyear = 0 + $dob->format('Y');
        $nowyear = Carbon::now()->year;
        $correctyear = $dobyear;
        $newbirth = $dob;
        if ($dobyear > $nowyear) {
            $correctyear = $dobyear - 100;
            $newbirth = Carbon::create($correctyear, 0 + $dob->format('m'), 0 + $dob->format('d'));
        }

        $newbirth = $newbirth->format('d-m-Y');

        $total_days = $plan->total_days;
        $addDays = (0 + $total_days) - 1;
        $depdate = new Carbon($orders->dep_date);
        $rtndate = new Carbon($orders->dep_date);
        $rtndate->addDays($addDays);
        $duration = "(".$depdate->format('d-m-Y').") TO (".$rtndate->format('d-m-Y').")";

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('payment.e-cert', compact('orders', 'plan', 'cert_number', 'url_bg', 'newbirth', 'duration'));
        return $pdf->stream();
    }
}