@extends('layouts.master')

@section('title') PLAN LIST @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') ADMIN @endslot
        @slot('title') PLAN LIST @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            @if($errors->any())
                                <h4 style="color:red;">{{$errors->first()}}</h4>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4" style="text-align: left;">
                            <a href="{{ route('plan_add') }}" class="btn btn-primary w-md" >Add Plan</a>
                        </div>
                        <div class="col-md-4" style="text-align: right;">
                        </div>  
                        <div class="col-md-4" style="text-align: right;">
                            <a href="{{ route('plan_list') }}" class="btn btn-primary w-md">Refresh</a>
                        </div>                          
                    </div>
                    <br>

                    <div>
                        <table id="datatable" class="table table-bordered dt-responsive w-100">
                            <thead>
                                <tr>
                                    <th data-priority="0" width="5%">#</th>
                                    <th data-priority="1" width="12%">Name</th>
                                    <th data-priority="1" width="10%">Price (RM)</th>
                                    <th data-priority="1" width="12%">Addt. Day Price (RM)</th>
                                    <th data-priority="1" width="10%">Coverage Days</th>
                                    <th data-priority="1" width="20%">Description</th>
                                    <th data-priority="1" width="5%">Quantity</th>
                                    <th data-priority="3" width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($plans as $i => $plan)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $plan->name }}</td>
                                        <td>{{ $plan->price }}</td>
                                        <td>{{ $plan->price_per_day }}</td>
                                        <td>{{ $plan->total_days }}</td>
                                        <td>{{ $plan->description }}</td>
                                        <td>
                                            @foreach ($orders as $i => $order) 
                                                @if (array_keys($order)[0] == $plan->name) 
                                                    {{ $order[$plan->name] }}
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="{{ route('plan_delete', $plan->id) }}" onclick="return confirm('Do you really want to delete?');" class="waves-effect" style="color: red;">
                                                <i class="bx bx-trash-alt font-size-20" title="Delete"></i>
                                            </a>

                                            <a href="{{ route('plan_edit', $plan->id) }}" class="waves-effect" style="color: purple;">
                                                <i class="bx bx-book-open font-size-20" title="Edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>

        function clicked(e, id)
        {
            // if(!confirm('Are you sure to submit?')) {
            //     e.preventDefault();
            // } else {
                $("#role" + id).change(function () {
                    var end = this.value;
                    var userId = id;
                    var firstDropVal = $('#role' + id).val();

                    $.ajax({
                        type:'get',
                        url:'/post_role' + '/' + firstDropVal + '/' + userId,
                        data:'_token = <?php echo csrf_token() ?>',
                        success:function(data) {
                            alert(data.Data)
                        }
                    });
                });
            // }
        }
        
    </script>
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
