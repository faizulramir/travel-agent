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
                    <form action="{{ route('user_edit_post', $id) }}" method="POST">
                        @csrf
                        <h4 class="card-title">User Information</h4>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">Email</label>
                                    <input class="form-control" type="email" name="email" value="{{ $user->email }}" placeholder="Enter Email" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Username</label>
                                    <input class="form-control" type="text" name="name" value="{{ $user->name }}" placeholder="Enter Username" required>
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
                                    <label for="plan">Role</label>
                                    <select id="role" name="role" class="form-control select2-search-disable"required>
                                        <option value="" {{ isset($user->getRoleNames()[0]) ? 'selected' : '' }}>Please Select</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ isset($user->getRoleNames()[0]) ? $user->getRoleNames()[0] == $role->name ? 'selected' : '' : ''}}>
                                                @if ($role->name == 'ind')
                                                    Individu
                                                @elseif ($role->name == 'ag')
                                                    Agent
                                                @elseif ($role->name == 'tra')
                                                    Travel Agent
                                                @elseif ($role->name == 'fin')
                                                    Finance
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if($errors->any())
                            <p style="color:red;">{{$errors->first()}}</p>
                        @endif
                        <br>
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
