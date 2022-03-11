<?php

namespace App\Http\Controllers;

use App\Models\DashboardUser;
use App\Models\FileUpload;
use App\Models\Order;
use App\Models\ReportAction;
use App\Models\Category;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $curUser = Auth::id();
        
        $checkUserRole = DashboardUser::where('id', $curUser)->first();
        //dd($request, $curUser, $checkUserRole, $user);

        $cust_tot_upl = 0;
        $cust_pen_sub = 0;
        $cust_pen_pay = 0;
        $cust_pen_doc = 0;

        $adm_tot_up = 0;
        $tra_tot_up = 0;
        $ag_tot_up = 0;
        $ind_tot_up = 0;

        $tot_sub = 0;
        $akc_tot_sub = 0;
        $tra_tot_sub = 0;
        $agn_tot_sub = 0;
        $ind_tot_sub = 0;

        $fin_inv = 0;
        $fin_pay = 0;
        $akc_app = 0;
        $akc_doc = 0;

        $tot_jemaah = 0;
        $tot_lite = 0;
        $tot_basic = 0;
        $tot_standard = 0;
        $tot_premium = 0;
        $tot_pcr = 0;
        $tot_tpa = 0;
        $tot_can = 0;
        $tot_res = 0;

        $amt_inv = 0.00;
        $amt_pay = 0.00;

        $tot_cc = 0;
        $tot_fpx = 0;
        $tot_other = 0;

        $amt_cc = 0.00;
        $amt_fpx = 0.00;
        $amt_other = 0.00;

        $total_uploads = FileUpload::all();


        if (!empty($checkUserRole->getRoleNames()[0])) {
            $user = DashboardUser::where('id', $curUser)->first();
            //current / customer user
            if ($user->getRoleNames()[0] == 'tra' || $user->getRoleNames()[0] == 'ag' || $user->getRoleNames()[0] == 'ind') {
                $cust_tot_upl = FileUpload::where('user_id', $curUser)->where('status', '!=', '0')->get()->count();
                $cust_pen_sub = FileUpload::where('user_id', $curUser)->where('status', '=', '0')->get()->count();
                $cust_pen_pay = FileUpload::where('user_id', $curUser)->where('status', '=', '3')->get()->count();
                $cust_pen_doc = FileUpload::where('user_id', $curUser)->where('status', '!=', '0')->where('status', '!=', '99')->where('supp_doc', '=', null)->get()->count();
            }

            //admin
            if ($user->getRoleNames()[0] == 'akc' || $user->getRoleNames()[0] == 'fin' || $user->getRoleNames()[0] == 'mkh') {
                $uploads = FileUpload::all();
                if ($uploads) {
                    $tot_sub = FileUpload::where('status', '!=', '0')->get()->count();
                    foreach ($uploads as $i => $upload) {
                        $user = DashboardUser::where('id', $upload->user_id)->first();

                        if ($user->getRoleNames()[0] == 'tra' && $upload->status != '0') {
                            $tra_tot_sub = $tra_tot_sub + 1;
                        } else if ($user->getRoleNames()[0] == 'ag' && $upload->status != '0') {
                            $agn_tot_sub = $agn_tot_sub + 1;
                        } else if ($user->getRoleNames()[0] == 'ind' && $upload->status != '0') {
                            $ind_tot_sub = $ind_tot_sub + 1;
                        } else if ($user->getRoleNames()[0] == 'akc' || $user->getRoleNames()[0] == 'fin' || $user->getRoleNames()[0] == 'mkh') {
                            $akc_tot_sub = $akc_tot_sub + 1;
                        } 
                        
                        //finance
                        if ($upload->status == '2.1') {
                            $fin_inv = $fin_inv + 1;
                        }
                        if ($upload->status == '4') {
                            $fin_pay = $fin_pay + 1;
                        }  
                        if ($upload->status == '2') {
                            $akc_app = $akc_app + 1;
                        }  
                        if ($upload->status != '0' && $upload->status != '99' && $upload->supp_doc == null) {
                            $akc_doc = $akc_doc + 1;
                        } 

                        //counter
                        if ($upload->status == '5') {
                            $orders = Order::where('file_id', $upload->id)->where('status', '1')->get();
                            $orders1 = Order::where('file_id', $upload->id)->where('status', '0')->orWhere('status', '2')->get();
                            $orders2 = Order::where('file_id', $upload->id)->where('status', '3')->get();
                            //dd($orders, count($orders));
                            $tot_jemaah = $tot_jemaah + count($orders);
                            $tot_can = $tot_can + count($orders1);
                            $tot_res = $tot_res + count($orders2);
    
                            $orders = Order::where('file_id', $upload->id)->where('status', '1')->where('plan_type', 'LITE')->get();
                            $tot_lite = $tot_lite + count($orders);
    
                            $orders = Order::where('file_id', $upload->id)->where('status', '1')->where('plan_type', 'BASIC')->get();
                            $tot_basic = $tot_basic + count($orders);     
                            
                            $orders = Order::where('file_id', $upload->id)->where('status', '1')->where('plan_type', 'STANDARD')->get();
                            $tot_standard = $tot_standard + count($orders); 
    
                            $orders = Order::where('file_id', $upload->id)->where('status', '1')->where('plan_type', 'PREMIUM')->get();
                            $tot_premium = $tot_premium + count($orders); 
    
                            $orders = Order::where('file_id', $upload->id)->where('status', '1')->where('pcr', 'PCR')->get();
                            $tot_pcr = $tot_pcr + count($orders);
    
                            $orders = Order::where('file_id', $upload->id)->where('status', '1')->where('tpa', 'LIKE', 'TPA%')->get();
                            $tot_tpa = $tot_tpa + count($orders); 
                        } 

                        //amount: still invoices
                        if ($upload->status == '3') {
                            if ($upload->json_inv) {
                                $data_decode = json_decode($upload->json_inv, true);
                                $tot_inv2 = $data_decode['tot_inv2'];
                                if ($tot_inv2) {
                                    $amt_inv = 0 + $amt_inv + $tot_inv2;
                                }
                            }
                        }

                        //amount: already paid
                        if ($upload->status == '4' || $upload->status == '5') {
                            if ($upload->json_inv) {
                                $data_decode = json_decode($upload->json_inv, true);
                                $tot_inv2 = $data_decode['tot_inv2'];
                                if ($tot_inv2) {
                                    $amt_pay = 0 + $amt_pay + $tot_inv2;
                                }
                                
                                $str_1 = str_replace("RM", "", $upload->pay->pay_total);
                                $str_2 = str_replace(",", "", $str_1);
                                $str_3 = str_replace(" ", "", $str_2);
                                $pay_totals = str_replace(".00", "", $str_3);

                                if ($upload->pay->pay_by == 'OTHER') {
                                    $tot_other = $tot_other + 1;
                                    $amt_other = $amt_other + $pay_totals;
                                } else if ($upload->pay->pay_by == 'CC') {
                                    $tot_cc = $tot_cc + 1;
                                    $amt_cc = $amt_cc + $pay_totals;
                                }  else if ($upload->pay->pay_by == 'FPX') {
                                    $tot_fpx = $tot_fpx + 1;
                                    $amt_fpx = $amt_fpx + $pay_totals;
                                }
                            }
                        }                        
                    }
                }
            }
        }


        /*
        if (!empty($checkUserRole->getRoleNames()[0])) {
            //dd($curUser);

            $uploads = FileUpload::all();
            //$uploads = FileUpload::where('user_id', $curUser)->get();
            //dd($uploads);

            $total_uploads = $uploads;
            $tra_uploads = 0;
            $agn_uploads = 0;
            $diy_uploads = 0;
            $akc_uploads = 0;
            $tra_docs = 0;
            $tra_pays = 0;

            //count file uploads
            if ($uploads) {
                foreach ($uploads as $i => $upload) {
                    $user = DashboardUser::where('id', $upload->user_id)->first();

                    if ($user->getRoleNames()[0] == 'tra' && $upload->status != '0') {
                        $tra_uploads = $tra_uploads + 1;
                        if ($user->id == $upload->user_id) {
                            if ($upload->status == '3') {
                                $tra_pays = $tra_pays + 1;
                            }
                        }
                    } else if ($user->getRoleNames()[0] == 'ag' && $upload->status != '0') {
                        $agn_uploads = $agn_uploads + 1;
                    } else if ($user->getRoleNames()[0] == 'ind' && $upload->status != '0') {
                        $diy_uploads = $diy_uploads + 1;
                    } else if ($user->getRoleNames()[0] == 'akc' || $user->getRoleNames()[0] == 'fin') {
                        $akc_uploads = $akc_uploads + 1;
                    }                     
                }
            }

            $fin_inv = 0;
            $fin_pay = 0;
            $akc_app = 0;
            $akc_doc = 0;

            //count invoice/payment
            $user = DashboardUser::where('id', $curUser)->first();
            if ($user->getRoleNames()[0] == 'fin' || $user->getRoleNames()[0] == 'akc') {
                $uploads = FileUpload::all();
                if ($uploads) {
                    foreach ($uploads as $i => $upload) {
                        //echo $upload->status;
                        if ($upload->status == '2.1') {
                            $fin_inv = $fin_inv + 1;
                        }
                        if ($upload->status == '4') {
                            $fin_pay = $fin_pay + 1;
                        }  
                        if ($upload->status == '2') {
                            $akc_app = $akc_app + 1;
                        }  
                        if ($upload->status != '0' && $upload->status != '99' && $upload->supp_doc == null) {
                            $akc_doc = $akc_doc + 1;
                        }                                      
                    }
                }
            }


            $tot_jemaah = 0;
            $tot_lite = 0;
            $tot_basic = 0;
            $tot_standard = 0;
            $tot_premium = 0;
            $tot_pcr = 0;
            $tot_tpa = 0;
            $tot_can = 0;
            $tot_res = 0;

            //count number of Jemaah
            $alluploads = FileUpload::where('status', '5')->get();
            //dd($alluploads);
            if ($alluploads) {
                foreach ($alluploads as $i => $allupload) {
                    if ($allupload->status != '99') {
                        $orders = Order::where('file_id', $allupload->id)->where('status', '1')->get();
                        $orders1 = Order::where('file_id', $allupload->id)->where('status', '0')->orWhere('status', '2')->get();
                        $orders2 = Order::where('file_id', $allupload->id)->where('status', '3')->get();
                        //dd($orders, count($orders));
                        $tot_jemaah = $tot_jemaah + count($orders);
                        $tot_can = $tot_can + count($orders1);
                        $tot_res = $tot_res + count($orders2);

                        $orders = Order::where('file_id', $allupload->id)->where('status', '1')->where('plan_type', 'LITE')->get();
                        $tot_lite = $tot_lite + count($orders);

                        $orders = Order::where('file_id', $allupload->id)->where('status', '1')->where('plan_type', 'BASIC')->get();
                        $tot_basic = $tot_basic + count($orders);     
                        
                        $orders = Order::where('file_id', $allupload->id)->where('status', '1')->where('plan_type', 'STANDARD')->get();
                        $tot_standard = $tot_standard + count($orders); 

                        $orders = Order::where('file_id', $allupload->id)->where('status', '1')->where('plan_type', 'PREMIUM')->get();
                        $tot_premium = $tot_premium + count($orders); 

                        $orders = Order::where('file_id', $allupload->id)->where('status', '1')->where('pcr', 'PCR')->get();
                        $tot_pcr = $tot_pcr + count($orders);

                        $orders = Order::where('file_id', $allupload->id)->where('status', '1')->where('tpa', 'LIKE', 'TPA%')->get();
                        $tot_tpa = $tot_tpa + count($orders); 
                    }               
                }
            }

            $amt_inv = 0.00;
            $amt_pay = 0.00;

            //calculate amount of invoice, paid payment
            $alluploads = FileUpload::where('status', '3')->orWhere('status', '4')->orWhere('status', '5')->get();
            if ($alluploads) {
                foreach ($alluploads as $i => $allupload) {
                    //still invoices
                    if ($allupload->status == '3') {
                        if ($allupload->json_inv) {
                            $data_decode = json_decode($allupload->json_inv, true);
                            $tot_inv2 = $data_decode['tot_inv2'];
                            if ($tot_inv2) {
                                $amt_inv = 0 + $amt_inv + $tot_inv2;
                            }
                        }
                    }
                    //already paid
                    if ($allupload->status == '4' || $allupload->status == '5') {
                        if ($allupload->json_inv) {
                            $data_decode = json_decode($allupload->json_inv, true);
                            $tot_inv2 = $data_decode['tot_inv2'];
                            if ($tot_inv2) {
                                $amt_pay = 0 + $amt_pay + $tot_inv2;
                            }
                        }
                    }
                }
            }
        }
        //dd($tot_jemaah, $tot_lite, $tot_basic, $tot_standard, $tot_premium, $tot_pcr, $tot_tpa, $amt_inv, $amt_pay);
        */

        if (view()->exists($request->path())) {
            if (!empty($checkUserRole->getRoleNames()[0])) {
                return view($request->path(), compact(  
                                                        //'total_uploads', 'agn_uploads', 'diy_uploads', 'tra_uploads', 'akc_uploads', 'tra_pays', 'tra_docs', 
                                                        'total_uploads', 
                                                        'fin_inv', 'fin_pay', 'tot_jemaah', 'tot_lite', 'tot_basic', 'tot_standard', 
                                                        'tot_premium', 'tot_pcr', 'tot_tpa', 'tot_can', 'tot_res', 'amt_inv', 'amt_pay',
                                                        'akc_app', 'akc_doc',
                                                    
                                                        'cust_tot_upl', 'cust_pen_sub', 'cust_pen_pay', 'cust_pen_doc',
                                                        'akc_tot_sub', 'tra_tot_sub', 'agn_tot_sub', 'ind_tot_sub',
                                                        'tot_sub',

                                                        'tot_cc', 'tot_fpx', 'tot_other', 'amt_cc', 'amt_fpx', 'amt_other'
                                                    
                                                    
                                                    ));
            } else {
                return view($request->path());
            }
        }
        return abort(404);
    }

    public function root(Request $request)
    {
        return redirect()->route('index');
    }

    /*Language Translation*/
    public function lang($locale)
    {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }

    public function updateProfile(Request $request, $id)
    {
        // return $request->all();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'dob' => ['required', 'date', 'before:today'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
            'role' => ['required', 'string' ,'max:255'],
        ]);

        $user = User::find($id);
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->dob = date('Y-m-d', strtotime($request->get('dob')));
        $user->role = $request->get('role');

        $user->update();
        if ($user) {
            Session::flash('message', 'User Details Updated successfully!');
            Session::flash('alert-class', 'alert-success');
            return response()->json([
                'isSuccess' => true,
                'Message' => "User Details Updated successfully!"
            ], 200); // Status code here
        } else {
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
            return response()->json([
                'isSuccess' => true,
                'Message' => "Something went wrong!"
            ], 200); // Status code here
        }
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json([
                'isSuccess' => false,
                'Message' => "Your Current password does not matches with the password you provided. Please try again."
            ], 200); // Status code 
        } else {
            $user = DashboardUser::find($id);
            $user->password = Hash::make($request->get('password'));
            $user->update();
            if ($user) {
                Session::flash('message', 'Password updated successfully!');
                Session::flash('alert-class', 'alert-success');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Password updated successfully!"
                ], 200); // Status code here
            } else {
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Something went wrong!"
                ], 200); // Status code here
            }
        }
    }

    public function search_dashboard() 
    {
        $search_by = request()->post('search_by');
        $search_val = request()->post('search_val');
        $data = '';
        if($search_by && $search_val) {
            if($search_by == 'ecert') {
                $data = Order::where('ecert', 'like', "%$search_val%")->get();
            } else if ($search_by == 'name') {
                $data = Order::where('name', 'like', "%$search_val%")->get();
            } else if ($search_by == 'passport') {
                $data = Order::where('passport_no', 'like', "%$search_val%")->get();
            } else if ($search_by == 'agent_name') {
                $data = FileUpload::where('ta_name', 'like', "%$search_val%")->get();
            } else if ($search_by == 'invoice') {
                $data = Order::where('invoice', 'like', "%$search_val%")->get();
            } else if ($search_by == 'ic') {
                $data = Order::where('ic_no', 'like', "%$search_val%")->get();
            }
        }
        return response()->json([
            'isSuccess' => true,
            'Data' => $data
        ], 200);
    }

    public function excel_detail_home($id) 
    {
        if (Auth::user()->getRoleNames()[0] == 'tra') {
            return redirect()->route('excel_detail', ['id' => $id]);
        } else if (Auth::user()->getRoleNames()[0] == 'ind') {
            return redirect()->route('application_detail', ['id' => $id]);
        } else if (Auth::user()->getRoleNames()[0] == 'ag') {
            return redirect()->route('excel_detail_agent', ['id' => $id]);
        } else if (Auth::user()->getRoleNames()[0] == 'admin') {
            return redirect()->route('excel_detail_admin', ['id' => $id]);
        } 
    }

    
}
