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
        $uploads = FileUpload::all();
        $total_uploads = $uploads;
        $tra_uploads = 0;
        $agent_uploads = 0;
        $diy_uploads = 0;

        foreach ($uploads as $i => $upload) {
            $user = DashboardUser::where('id', $upload->user_id)->first();
            if ($user->getRoleNames()[0] == 'tra') {
                $tra_uploads = $tra_uploads + 1;
            } else if ($user->getRoleNames()[0] == 'ag') {
                $agent_uploads = $agent_uploads + 1;
            } else if ($user->getRoleNames()[0] == 'ind') {
                $diy_uploads = $diy_uploads + 1;
            } 
        }

        if (view()->exists($request->path())) {
            return view($request->path(), compact('total_uploads', 'agent_uploads', 'diy_uploads', 'tra_uploads'));
        }
        return abort(404);
    }

    public function root()
    {
        $uploads = FileUpload::all();
        $total_uploads = $uploads;
        $tra_uploads = 0;
        $agent_uploads = 0;
        $diy_uploads = 0;

        foreach ($uploads as $i => $upload) {
            $user = DashboardUser::where('id', $upload->user_id)->first();
            if ($user->getRoleNames()[0] == 'tra') {
                $tra_uploads = $tra_uploads + 1;
            } else if ($user->getRoleNames()[0] == 'ag') {
                $agent_uploads = $agent_uploads + 1;
            } else if ($user->getRoleNames()[0] == 'ind') {
                $diy_uploads = $diy_uploads + 1;
            } 
        }
        return view('index', compact('total_uploads', 'agent_uploads', 'diy_uploads', 'tra_uploads'));
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
            $user = User::find($id);
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
                $files = FileUpload::where('ta_name', 'like', "%$search_val%")->get();
                foreach ($files as $i => $file) {
                    $data = Order::where('file_id', $file->id)->get();
                }
            } else if ($search_by == 'invoice') {
                $data = Order::where('invoice', 'like', "%$search_val%")->get();
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
