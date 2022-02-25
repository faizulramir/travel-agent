@extends('layouts.master')

@section('title') CLAIM LIST @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') ADMIN @endslot
        @slot('title') CLAIM LIST @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6" style="text-align: left;">
                            <a href="{{ route('claim_list') }}" class="btn btn-primary w-md">
                                <i class="bx bx-chevrons-left font-size-24" title="Back"></i>
                            </a>
                        </div>
                    </div>
                    <br>
                    <div>
                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th data-priority="1">Name</th>
                                    <th data-priority="3">Passport No</th>
                                    <th data-priority="3">DEP Date</th>
                                    <th data-priority="3">RTN Date</th>
                                    <th data-priority="3">ECare</th>
                                    <th data-priority="3">PCR Date</th>
                                    <th data-priority="3">TPA</th>
                                    <th data-priority="1">Quarantine</th>
                                    <th data-priority="3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $i => $order)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $order->name }}</td>
                                        <td>{{ $order->passport_no }}</td>
                                        <td>{{ $order->dep_date ? date('d-m-Y', strtotime($order->dep_date)) : '' }}</td>
                                        <td>{{ $order->return_date ? date('d-m-Y', strtotime($order->return_date)): '' }}</td>
                                        <td>{{ $order->plan_type }} {{ ($order->plan_type!='NO'? '('.$order->ecert.')' : '') }}</td>
                                        <td>
                                            @php
                                                try {
                                                    $pcr_date = $order->pcr_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $order->pcr_date)->format('Y-m-d') : '';
                                                } catch (\Throwable $th) {
                                                    $pcr_date =  date('Y-m-d', strtotime($order->pcr_date));
                                                }
                                                
                                                $rtn_date = date('Y-m-d', strtotime($order->return_date));
                                                if ($pcr_date == $rtn_date)
                                                    $temp_date =  date('Y-m-d', strtotime('-2 day', strtotime($pcr_date)));
                                                else {
                                                    $temp_date =  date('Y-m-d', strtotime($pcr_date));
                                                }
                                            @endphp
                                            <input type="date" class="form-control" name="pcr_date{{$order->id}}" value="{{$temp_date}}" id="pcr_date{{$order->id}}" onclick="clicked(event, {{$order->id}})" max="{{ $rtn_date }}">
                                        </td>
                                        <td>{{ $order->tpa }}</td>
                                        <td>{{ ($order->pcr_result == '0' || $order->pcr_result == null) ? 'NO' : 'YES' }}</td>
                                        <td>
                                            <a href="#" class="waves-effect" style="color: blue;">
                                                <input type="file" name="add_pcr{{$order->id}}" id="add_pcr{{$order->id}}" style="display: none;">
                                                <i onclick="openDetail({{$order->id}})" id="uploadPCR{{$order->id}}" class="bx bxs-cloud-upload font-size-24" title="Upload PCR Result"></i>
                                            </a>
                                            @if ($order->pcr_file_name !== null) 
                                                <a href="{{ route('downloadPCR', [$order->user_id, $order->id, $order->pcr_file_name]) }}" class="waves-effect" style="color: green;">
                                                    <i id="downloadPCR{{$order->id}}" class="bx bxs-cloud-download font-size-24" title="Download PCR Result"></i>
                                                </a>
                                            @endif

                                            @if ($order->upload->status == '5')
                                                @if ($order->plan_type != 'NO' &&  $order->status == '1')
                                                    <a href="{{ route('create_cert_ind', $order->id) }}" class="waves-effect" style="color: green;" target="_blank">
                                                        <i class="bx bx-food-menu font-size-24" title="Print ECert"></i>
                                                    </a>
                                                @endif                                                
                                            @endif

                                            <a href="{{ route('claim_edit', $order->id) }}" class="waves-effect" style="color: black;">
                                                <i class="bx bx-edit-alt font-size-24" title="Edit Claim"></i>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.min.js" integrity="sha512-BMIFH0QGwPdinbGu7AraCzG9T4hKEkcsbbr+Uqv8IY3G5+JTzs7ycfGbz7Xh85ONQsnHYrxZSXgS1Pdo9r7B6w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xls/0.7.6/xls.min.js" integrity="sha512-Nqu6bagCq6Jp2ZhezdTFaomiZBZYVhzafGww9teXy1xsvhfpw1ZW3FlVqMazRfLKPVWucbeBXNY5MgO925fpoQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function openDetail (id) {
            $(document).ready(function() {
                var supp_id = id;
                $("#add_pcr" + id).val(null);
                $("#add_pcr" + id).trigger("click");

                $("#add_pcr" + supp_id).change(function () {
                    var form_data = new FormData();
                    form_data.append("file", $("#add_pcr" + supp_id)[0].files[0]);
                    form_data.append("id", supp_id);
                    $.ajax({
                        url: '/post_pcr_doc',
                        type: 'POST',
                        data: form_data,
                        dataType: 'JSON',
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            alert(data.Data);
                            location.reload();
                        }
                    });
                });
            });
            return supp_id;
        }

        function clicked(e, id) {
            $("#pcr_date" + id).change(function () {
                var end = this.value;
                var userId = id;
                var firstDropVal = $('#pcr_date' + id).val();

                $.ajax({
                    type:'get',
                    url:'/post_return_date' + '/' + firstDropVal + '/' + userId,
                    data:'_token = <?php echo csrf_token() ?>',
                    success:function(data) {
                        alert(data.Data)
                        location.reload();
                    }
                });
            });
        }

    </script>
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
    <!-- Responsive Table js -->
    <script src="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.js') }}"></script>

    <!-- Init js -->
    <script src="{{ URL::asset('/assets/js/pages/table-responsive.init.js') }}"></script>
@endsection
