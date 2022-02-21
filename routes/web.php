<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//ecert public
Route::get('/ecert/search', [App\Http\Controllers\PublicController::class, 'search_cert_public'])->name('search_cert_public');
Route::post('/ecert/post', [App\Http\Controllers\PublicController::class, 'post_cert_public'])->name('post_cert_public');
Route::get('/download_cert_public/{order_id}', [App\Http\Controllers\PublicController::class, 'download_cert_public'])->name('download_cert_public');



Auth::routes();

//finance
Route::get('/excel_list_finance', [App\Http\Controllers\FinanceController::class, 'excel_list_finance'])->name('excel_list_finance');
Route::get('/payment_detail/{id}', [App\Http\Controllers\FinanceController::class, 'payment_detail'])->name('payment_detail');
Route::post('/endorse_payment/{id}', [App\Http\Controllers\FinanceController::class, 'endorse_payment'])->name('endorse_payment');
Route::get('/download_payment/{user_id}/{file_id}', [App\Http\Controllers\FinanceController::class, 'download_payment'])->name('download_payment');
Route::get('/excel_detail_finance/{id}', [App\Http\Controllers\FinanceController::class, 'excel_detail_finance'])->name('excel_detail_finance');
Route::get('/invoice/reject/{id}', [App\Http\Controllers\FinanceController::class, 'invoice_reject'])->name('invoice_reject');


//payment
Route::get('/payment/{id}', [App\Http\Controllers\PaymentController::class, 'payment'])->name('payment');
Route::get('/ecert_all/{id}', [App\Http\Controllers\PaymentController::class, 'ecert_all'])->name('ecert_all');
Route::get('/download_all_cert/{id}', [App\Http\Controllers\PaymentController::class, 'download_all_cert'])->name('download_all_cert');
Route::get('/delete_all_cert/{id}', [App\Http\Controllers\PaymentController::class, 'delete_all_cert'])->name('delete_all_cert');
Route::get('/create_invoice_ind/{order_id}', [App\Http\Controllers\PaymentController::class, 'create_invoice_ind'])->name('create_invoice_ind');
Route::get('/create_invoice/{order_id}', [App\Http\Controllers\PaymentController::class, 'create_invoice'])->name('create_invoice');
Route::get('/create_cert_ind/{order_id}', [App\Http\Controllers\PaymentController::class, 'create_cert_ind'])->name('create_cert_ind');
Route::post('/submit_payment', [App\Http\Controllers\PaymentController::class, 'submit_payment'])->name('submit_payment');

//travel agent
Route::get('/excel_list', [App\Http\Controllers\TravelAgentController::class, 'excel_list'])->name('excel_list');
Route::get('/excel_detail/{id}', [App\Http\Controllers\TravelAgentController::class, 'excel_detail'])->name('excel_detail');
Route::post('/excel_post_ta', [App\Http\Controllers\TravelAgentController::class, 'excel_post_ta'])->name('excel_post_ta');
Route::post('/supp_doc_post_ta', [App\Http\Controllers\TravelAgentController::class, 'supp_doc_post_ta'])->name('supp_doc_post_ta');
Route::post('/submit_post_ta', [App\Http\Controllers\TravelAgentController::class, 'submit_post_ta'])->name('submit_post_ta');
Route::get('/delete_excel_ta/{id}', [App\Http\Controllers\TravelAgentController::class, 'delete_excel_ta'])->name('delete_excel_ta');
Route::get('/excel_detail_ta/{id}', [App\Http\Controllers\TravelAgentController::class, 'excel_detail_ta'])->name('excel_detail_ta');
Route::get('/update_detail_ta/{id}/{status}', [App\Http\Controllers\TravelAgentController::class, 'update_detail_ta'])->name('update_detail_ta');

//agent
Route::get('/excel_list_agent', [App\Http\Controllers\AgentController::class, 'excel_list_agent'])->name('excel_list_agent');
Route::post('/excel_post_agent', [App\Http\Controllers\AgentController::class, 'excel_post_agent'])->name('excel_post_agent');
Route::post('/supp_doc_post_agent', [App\Http\Controllers\AgentController::class, 'supp_doc_post_agent'])->name('supp_doc_post_agent');
Route::post('/submit_post_agent', [App\Http\Controllers\AgentController::class, 'submit_post_agent'])->name('submit_post_agent');
Route::get('/download_cert_agent', [App\Http\Controllers\AgentController::class, 'download_cert_agent'])->name('download_cert_agent');
Route::get('/download_invoice_agent', [App\Http\Controllers\AgentController::class, 'download_invoice_agent'])->name('download_invoice_agent');
Route::get('/delete_excel_agent/{id}', [App\Http\Controllers\AgentController::class, 'delete_excel_agent'])->name('delete_excel_agent');
Route::get('/excel_detail_agent/{id}', [App\Http\Controllers\AgentController::class, 'excel_detail_agent'])->name('excel_detail_agent');
Route::get('/update_detail_agent/{id}/{status}', [App\Http\Controllers\AgentController::class, 'update_detail_agent'])->name('update_detail_agent');

//admin
Route::get('/excel_list_admin', [App\Http\Controllers\AdminController::class, 'excel_list_admin'])->name('excel_list_admin');
Route::get('/setting_admin', [App\Http\Controllers\AdminController::class, 'setting_admin'])->name('setting_admin');
Route::get('/getImg/{filename}', [App\Http\Controllers\AdminController::class, 'getImg'])->name('getImg');
Route::post('/excel_post_admin', [App\Http\Controllers\AdminController::class, 'excel_post_admin'])->name('excel_post_admin');
Route::get('/download_excel/{id}', [App\Http\Controllers\AdminController::class, 'download_excel'])->name('download_excel');
Route::get('/user_list', [App\Http\Controllers\AdminController::class, 'user_list'])->name('user_list');
Route::get('/user_add', [App\Http\Controllers\AdminController::class, 'user_add'])->name('user_add');
Route::post('/user_add_post', [App\Http\Controllers\AdminController::class, 'user_add_post'])->name('user_add_post');
Route::get('/user_edit/{id}', [App\Http\Controllers\AdminController::class, 'user_edit'])->name('user_edit');
Route::post('/user_edit_post/{id}', [App\Http\Controllers\AdminController::class, 'user_edit_post'])->name('user_edit_post');
Route::get('/user_delete/{id}', [App\Http\Controllers\AdminController::class, 'user_delete'])->name('user_delete');
Route::get('/plan_list', [App\Http\Controllers\AdminController::class, 'plan_list'])->name('plan_list');
Route::get('/plan_add', [App\Http\Controllers\AdminController::class, 'plan_add'])->name('plan_add');
Route::get('/plan_delete/{id}', [App\Http\Controllers\AdminController::class, 'plan_delete'])->name('plan_delete');
Route::post('/post_plan', [App\Http\Controllers\AdminController::class, 'post_plan'])->name('post_plan');
Route::get('/download_supp_doc/{user_id}/{file_id}', [App\Http\Controllers\AdminController::class, 'download_supp_doc'])->name('download_supp_doc');
Route::get('/post_role/{role_id}/{user_id}', [App\Http\Controllers\AdminController::class, 'post_role'])->name('post_role');
Route::get('/excel_detail_admin/{id}', [App\Http\Controllers\AdminController::class, 'excel_detail_admin'])->name('excel_detail_admin');
Route::get('/update_excel_status_admin/{id}/{status}', [App\Http\Controllers\AdminController::class, 'update_excel_status_admin'])->name('update_excel_status_admin');
Route::get('/admin_payment_detail/{id}', [App\Http\Controllers\AdminController::class, 'admin_payment_detail'])->name('admin_payment_detail');
Route::post('/change_ecert_background', [App\Http\Controllers\AdminController::class, 'change_ecert_background'])->name('change_ecert_background');
Route::post('/change_excel_template', [App\Http\Controllers\AdminController::class, 'change_excel_template'])->name('change_excel_template');
Route::post('/post_plan_edit/{id}', [App\Http\Controllers\AdminController::class, 'post_plan_edit'])->name('post_plan_edit');
Route::get('/plan_edit/{id}', [App\Http\Controllers\AdminController::class, 'plan_edit'])->name('plan_edit');
Route::get('/ssm_cert_download/{id}', [App\Http\Controllers\AdminController::class, 'ssm_cert_download'])->name('ssm_cert_download');
Route::get('/jemaah/{id}', [App\Http\Controllers\AdminController::class, 'jemaah_show'])->name('jemaah_show');
Route::post('/jemaah/edit/{id}', [App\Http\Controllers\AdminController::class, 'jemaah_edit'])->name('jemaah_edit');
Route::post('/post_edit_ta_name', [App\Http\Controllers\AdminController::class, 'post_edit_ta_name'])->name('post_edit_ta_name');
Route::post('/post_edit_cert_no', [App\Http\Controllers\AdminController::class, 'post_edit_cert_no'])->name('post_edit_cert_no');
Route::post('/supp_doc_post_admin', [App\Http\Controllers\AdminController::class, 'supp_doc_post_admin'])->name('supp_doc_post_admin');
Route::get('/supp_doc_download_admin/{id}/{type}', [App\Http\Controllers\AdminController::class, 'supp_doc_download_admin'])->name('supp_doc_download_admin');
Route::get('/supp_doc_check/{id}/{type}', [App\Http\Controllers\AdminController::class, 'supp_doc_check'])->name('supp_doc_check');
Route::get('/invoice/cancel/{id}', [App\Http\Controllers\AdminController::class, 'cancel_invoice'])->name('cancel_invoice');

//individu
Route::get('/application', [App\Http\Controllers\IndividuController::class, 'application'])->name('application');
Route::get('/application_list', [App\Http\Controllers\IndividuController::class, 'application_list'])->name('application_list');
Route::post('/application_post', [App\Http\Controllers\IndividuController::class, 'application_post'])->name('application_post');
Route::post('/application_post_excel', [App\Http\Controllers\IndividuController::class, 'application_post_excel'])->name('application_post_excel');
Route::get('/application_detail/{id}', [App\Http\Controllers\IndividuController::class, 'application_detail'])->name('application_detail');
Route::post('/submit_post_ind', [App\Http\Controllers\IndividuController::class, 'submit_post_ind'])->name('submit_post_ind');
Route::get('/application_delete/{id}', [App\Http\Controllers\IndividuController::class, 'application_delete'])->name('application_delete');
Route::post('/supp_doc_post_ind', [App\Http\Controllers\IndividuController::class, 'supp_doc_post_ind'])->name('supp_doc_post_ind');

Route::get('/download_template', [App\Http\Controllers\TravelAgentController::class, 'download_template'])->name('download_template');
Route::get('/download_cert', [App\Http\Controllers\TravelAgentController::class, 'download_cert'])->name('download_cert');
Route::get('/download_invoice', [App\Http\Controllers\TravelAgentController::class, 'download_invoice'])->name('download_invoice');

Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');

//detail
Route::get('/upload_detail/{id}', [App\Http\Controllers\UploadDetailController::class, 'upload_detail'])->name('upload_detail');

//stripe
Route::get('/stripe/{pay_id}/{pay_total}/{pay_name}', [App\Http\Controllers\StripeController::class, 'stripe'])->name('stripe');
Route::post('/stripePost', [App\Http\Controllers\StripeController::class, 'stripePost'])->name('stripePost');

//dashboard
Route::post('/search_dashboard', [App\Http\Controllers\HomeController::class, 'search_dashboard'])->name('search_dashboard');
Route::get('/excel_detail_home/{id}', [App\Http\Controllers\HomeController::class, 'excel_detail_home'])->name('excel_detail_home');

//Update User Details
Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');

Route::get('/index', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

//Language Translation
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

//pcr
Route::get('/downloadPCR/{user_id}/{order_id}/{file_name}', [App\Http\Controllers\PcrController::class, 'downloadPCR'])->name('downloadPCR');
Route::get('/pcr_excel_list', [App\Http\Controllers\PcrController::class, 'pcr_excel_list'])->name('pcr_excel_list');
Route::get('/excel_detail_pcr/{id}', [App\Http\Controllers\PcrController::class, 'excel_detail_pcr'])->name('excel_detail_pcr');
Route::get('/post_return_date/{val}/{id}', [App\Http\Controllers\PcrController::class, 'post_return_date'])->name('post_return_date');
Route::post('/post_pcr_doc', [App\Http\Controllers\PcrController::class, 'post_pcr_doc'])->name('post_pcr_doc');

//email
Route::post('/send_mail/{subject_to_send}/{to_name}/{to_email}/{body}/{title}', [App\Http\Controllers\EmailController::class, 'send_mail'])->name('send_mail');