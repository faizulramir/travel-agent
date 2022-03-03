@extends('layouts.master')

@section('title') USER EDIT @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') ADMIN @endslot
        @slot('title') USER EDIT @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-6" style="text-align: left;">
                        <a href="{{ route('user_list') }}" class="btn btn-primary w-md">
                            <i class="bx bx-chevrons-left font-size-20" title="Back"></i>
                        </a>
                    </div>
                    <br>
                    
                    <form action="{{ route('user_edit_post', $id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <h4 class="card-title">User Information</h4>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">Email (Login ID)</label>
                                    <input class="form-control" type="email" name="email" value="{{ $user->email }}" placeholder="Enter Email" readonly>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">User Name</label>
                                    <input class="form-control" type="text" name="name" value="{{ $user->name }}" placeholder="Enter Username" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Role</label>
                                    <select id="role" name="role" class="form-control select2-search-disable"required>
                                        <option value="" {{ isset($user->getRoleNames()[0]) ? 'selected' : '' }}>Please Select</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ isset($user->getRoleNames()[0]) ? $user->getRoleNames()[0] == $role->name ? 'selected' : '' : ''}}>
                                                @if ($role->name == 'tra')
                                                    Travel Agent   
                                                @elseif ($role->name == 'ag')
                                                    DIY Agent                                                                                                 
                                                @elseif ($role->name == 'ind')
                                                    DIY Individu
                                                @elseif ($role->name == 'fin')
                                                    AKC Finance
                                                @elseif ($role->name == 'mkh')
                                                    AKC Makkah                                                    
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Date Of Birth</label>
                                    <input class="form-control" type="date" name="dob" value="{{ $user->dob }}" placeholder="Enter DOB" required>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Company Name</label>
                                    <input class="form-control" type="text" name="company_name" value="{{ $user->company_name }}" placeholder="Enter Company Name" required>
                                </div>
                            </div>
                            <div class="col-lg-4" style="display: {{ $user->getRoleNames()[0] != 'tra' ? 'none': 'block' }}">
                                <div>
                                    <label for="plan">SSM/Company Number</label>
                                    <input class="form-control" type="text" name="ssm_no" value="{{ $user->ssm_no }}" placeholder="Enter SSM Number" required>
                                </div>
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">Contact Number</label>
                                    <input class="form-control" type="number" name="phone" value="{{ $user->phone }}" placeholder="Enter Contact Number" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Company Location</label>
                                    <input class="form-control" type="text" name="company_location" value="{{ $user->company_location }}" placeholder="Enter Company Location" required>
                                </div>
                            </div>                            
                            <div class="col-lg-2" style="display: {{ $user->getRoleNames()[0] != 'tra' ? 'none': 'block' }}">
                                <div>
                                    <label for="plan">SSM Certificate</label>
                                    <br>
                                    <input type="file" class="form-control" name="ssm_cert">
                                </div>
                            </div>
                            <div class="col-lg-2" style="display: {{ $user->getRoleNames()[0] != 'tra' ? 'none': 'block' }}">
                                <div>
                                    <label for="plan">Download</label>
                                    <a style="display: {{ $user->ssm_cert == null ? 'none': 'block' }}" href="{{ route('ssm_cert_download', $user->id) }}" class="btn btn-primary waves-effect waves-light">Download Cert</a>
                                </div>
                            </div>
                        </div>
                        <br>
                        <hr/>

                        <div class="row">
                            <div class="col-lg-4">
                                <label>Registered on: {{ $user->created_at }}</label>
                            </div>
                        </div>   
                        <br>

                        @if($errors->any())
                            <p style="color:red;">{{ $errors->first() }}</p>
                            <br>
                        @endif

                        <div class="col-lg-12">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
