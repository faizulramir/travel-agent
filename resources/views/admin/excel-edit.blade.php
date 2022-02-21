@extends('layouts.master')

@section('title') JEMAAH @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') ADMIN @endslot
        @slot('title') EDIT RECORD @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-6" style="text-align: left;">
                        <a href="{{ route('excel_detail_admin', $jemaah->file_id) }}" class="btn btn-primary w-md">
                            <i class="bx bx-chevrons-left font-size-24" title="Back"></i>
                        </a>
                    </div>

                    <br>
                    <form action="{{ route('jemaah_edit', $jemaah->id) }}" method="POST">
                        @csrf
                        <h4 class="card-title">Traveller Information</h4>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">Name</label>
                                    <input class="form-control" type="text" name="name" placeholder="Enter Name" value="{{ $jemaah->name }}">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Passport No</label>
                                    <input class="form-control" type="text" name="passport_no" value="{{ $jemaah->passport_no }}" placeholder="Enter Passport No">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">IC No</label>
                                    <input class="form-control" type="text" name="ic_no" value="{{ $jemaah->ic_no }}" placeholder="Enter IC No">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Date Of Birth</label>
                                    <input class="form-control" type="text" name="dob" value="{{ $jemaah->dob }}">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Existing Illness</label>
                                    {{--
                                    <select id="ex_illness" name="ex_illness" class="form-control select2-search-disable" required>
                                        <option value="NONE" {{ $jemaah->ex_illness == 'NONE' ? 'selected' : '' }}>NONE</option>
                                        <option value="YES" {{ $jemaah->ex_illness == 'YES' ? 'selected' : '' }}>YES</option>
                                    </select>
                                    --}}
                                    <input class="form-control" type="text" name="ex_illness" value="{{ $jemaah->ex_illness }}" placeholder="Enter Illness">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">HP No</label>
                                    <input class="form-control" type="text" name="hp_no" value="{{ $jemaah->hp_no }}" placeholder="Enter HP No">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Email</label>
                                    <input class="form-control" type="email" name="email" value="{{ $jemaah->email }}" placeholder="Enter Email">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Departure Date</label>
                                    <input class="form-control" type="date" name="dep_date" value="{{ $jemaah->dep_date ? date('Y-m-d', strtotime($jemaah->dep_date)): '' }}" {{ $jemaah->upload->status != '4' && $jemaah->upload->status != '5' ? '' : 'readonly' }}>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Return Date</label>
                                    <input class="form-control" type="date" name="return_date" value="{{ $jemaah->return_date ? date('Y-m-d', strtotime($jemaah->return_date)): '' }}" {{ $jemaah->upload->status != '4' && $jemaah->upload->status != '5' ? '' : 'readonly' }}>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Plan Type (ECare)</label>
                                    <select id="plan_type" style="{{ $jemaah->upload->status != '4' && $jemaah->upload->status != '5' ? '' : 'pointer-events: none;' }}" name="plan_type" class="form-control select2-search-disable" required {{ $jemaah->upload->status != '4' && $jemaah->upload->status != '5' ? '' : 'readonly' }}>
                                        <option value="NO" {{ $jemaah->plan_type == 'NO' ? 'selected' : '' }}>NO</option>
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan->name }}" {{ $jemaah->plan_type == $plan->name ? 'selected' : '' }}>{{ $plan->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">PCR</label>
                                    <select id="pcr" style="{{ $jemaah->upload->status != '4' && $jemaah->upload->status != '5' ? '' : 'pointer-events: none;' }}" name="pcr" class="form-control select2-search-disable" required {{ $jemaah->upload->status != '4' && $jemaah->upload->status != '5' ? '' : 'readonly' }}>
                                        <option value="NO" {{ $jemaah->pcr == 'NO' ? 'selected' : '' }}>NO</option>
                                        <option value="PCR" {{ $jemaah->pcr == 'PCR' ? 'selected' : '' }}>PCR</option>
                                    </select>
                                </div>
                            </div>                            
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">TPA</label>
                                    <select id="tpa" style="{{ $jemaah->upload->status != '4' && $jemaah->upload->status != '5' ? '' : 'pointer-events: none;' }}" name="tpa" class="form-control select2-search-disable" required {{ $jemaah->upload->status != '4' && $jemaah->upload->status != '5' ? '' : 'readonly' }}>
                                        <option value="NO" {{ $jemaah->tpa == 'NO' ? 'selected' : '' }}>NO</option>
                                        @foreach ($tpas as $tpa)
                                            <option value="{{ $tpa->name }}" {{ $jemaah->tpa == $tpa->name ? 'selected' : '' }}>{{ $tpa->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="jemaah_status">Status</label>
                                <select id="jemaah_status" name="jemaah_status" class="form-control select2-search-disable" required>
                                    <option value="1" {{ $jemaah->status == '1' ? 'selected' : ''}}>OK</option>
                                    <option value="0" {{ $jemaah->status == '0' ? 'selected' : ''}}>CANCELLATION</option>
                                    <option value="2" {{ $jemaah->status == '2' ? 'selected' : ''}}>UNBOARDING</option>
                                    <option value="3" {{ $jemaah->status == '3' ? 'selected' : ''}}>RESCHEDULE</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="col-lg-12 text-center">
                            <br>
                            <button class="btn btn-primary waves-effect waves-light" type="submit">Save Changes</button>
                        </div>
                        @if(session()->has('success'))
                            <div class="row text-center">
                                <div class="col-md-12">
                                    <br>
                                    <label style="color: green">{{ session()->get('success') }}</label>
                                </div>
                            </div>
                        @endif
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
