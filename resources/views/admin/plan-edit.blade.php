@extends('layouts.master')

@section('title') PLAN @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') ADMIN @endslot
        @slot('title') PLAN @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('post_plan_edit', $id) }}" method="POST">
                        @csrf
                        <h4 class="card-title">Plan Information</h4>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">Plan Name</label>
                                    <input class="form-control" type="text" name="plan_name" value="{{ $plan->name }}" placeholder="Enter Plan Name" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Plan Price (RM)</label>
                                    <input class="form-control" type="number" name="plan_price" value="{{ $plan->price }}" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Days Cover</label>
                                    <input class="form-control" type="number" name="total_days" value="{{ $plan->total_days }}" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Plan Price / Day (RM)</label>
                                    <input class="form-control" type="number" name="price_per_day" value="{{ $plan->price_per_day }}" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-8">
                                <div>
                                    <label class="form-label">Plan Description</label>
                                    <textarea class="form-control" name="plan_desc" required>{{ $plan->description }}</textarea>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="col-lg-12">
                            <br>
                            <button class="btn btn-primary waves-effect waves-light" type="submit">Submit Plan</button>
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
