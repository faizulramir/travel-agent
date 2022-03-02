@extends('layouts.master')

@section('title') APPLICATION @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') INDIVIDU @endslot
        @slot('title') APPLICATION @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('application_post') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <h4 class="card-title">Applicant Information</h4>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">Applicant Name</label>
                                    <input class="form-control" type="text" name="name" value="{{ auth()->user()->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">Passport Number</label>
                                    <input class="form-control" type="text" name="passport_no" placeholder="Enter Passport Number" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">IC Number</label>
                                    <input class="form-control" type="text" name="ic_no" placeholder="Enter IC No" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">Ex Illness</label>
                                    <input class="form-control" type="text" name="ex_ill" placeholder="Enter Ex Illness" value="NONE" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">Phone Number</label>
                                    <input class="form-control" type="text" name="phone_no" placeholder="Enter Phone No" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">Email</label>
                                    <input class="form-control" type="email" name="email" value="{{ auth()->user()->email }}" readonly>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Date Of Birth</label>
                                    <input class="form-control" type="text" name="dob" value="{{ auth()->user()->dob ? date('d-m-Y', strtotime(auth()->user()->dob)) : ''}}" readonly>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="dep_date">Departure Date</label>
                                    <input class="form-control" type="date" name="dep_date" min="{{ $min_date }}" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="return_date">Return Date</label>
                                    <input class="form-control" type="date" name="return_date" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Plan</label>
                                    <select id="plan" name="plan" class="form-control select2-search-disable" required>
                                        @foreach ($plans as $plan)
                                            <option value="{{$plan->id}}">{{ strtoupper($plan->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">TPA</label>
                                    <select id="tpa" name="tpa" class="form-control select2-search-disable" required>
                                        <option value="NO" selected>NO</option>
                                        @foreach($tpas as $tpa)
                                            <option value="{{ $tpa->id }}">{{ $tpa->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="col-lg-4">
                                <div>
                                    <label class="form-label">Additional Days</label>
                                    <input class="form-control" type="number" name="add_days" placeholder="Enter Additional Days">
                                </div>
                            </div> --}}
                            <div class="col-lg-4">
                                <label class="form-label">PCR</label>
                                <select id="pcr" name="pcr" class="form-control select2-search-disable" required>
                                    <option value="NO" selected>NO</option>
                                    <option value="YES">YES</option>
                                </select>
                            </div>
                            {{-- <div class="col-lg-4">
                                <div>
                                    <label class="form-label">Travel Agent Name</label>
                                    <input class="form-control" type="text" name="travel_agent" placeholder="Enter Travel Agent Name">
                                </div>
                            </div> --}}
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-12 text-center">
                                <br>
                                <button class="btn btn-primary waves-effect waves-light" type="button">Download Plan Brochure</button>
                            </div>
                        </div>
                        <hr>
                        <h4 class="card-title">Supporting Documents</h4>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">Passport</label>
                                    <input class="form-control" type="file" name="passport_file" accept="application/pdf" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">E-Visa</label>
                                    <input class="form-control" type="file" name="visa_file" accept="application/pdf" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">E-Ticketing</label>
                                    <input class="form-control" type="file" name="ticket_file" accept="application/pdf" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 text-center">
                            <br>
                            <button class="btn btn-primary waves-effect waves-light" type="submit">Submit Application</button>
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
