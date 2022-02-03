@extends('layouts.master')

@section('title') SETTING @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') ADMIN @endslot
        @slot('title') SETTING @endslot
    @endcomponent
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="card-title">E-Cert Background</div>
                            <img id="img1" src="{{ route('getImg', 'template_cert.png') }}" alt="img1" height="600px" width="400px" style="border:1px dashed #E0E0E0; cursor: pointer;">
                            <input type="file" id="img1_file" style="display: none;" accept="image/png" />
                        </div>
                        <div class="col-md-4">
                            
                        </div>
                        <div class="col-md-4">
                            <div class="card-title">Excel</div>
                            <br>
                            <label for="plan">Old Excel Template</label><br>
                            <a href="{{ route('download_template') }}" class="btn btn-primary w-md" target="_blank" title="Download current template">Download Old Template</a>
                            <br>
                            <br>
                            <label for="plan">Upload Excel Template</label>
                            <input class="form-control" type="file" id="excel_template_new" name="excel_template_new" required accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <p>Please click on image to change</p>
                        </div>
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

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $("img1_file").attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#img1").click(function() {
            $("input[id='img1_file']").click();
        });

        $("#img1_file").change(function () {
            readURL(this);
            var form_data = new FormData();
            form_data.append("img", $("#img1_file")[0].files[0]);
            
            $.ajax({
                url: '/change_ecert_background',
                type: 'POST',
                data: form_data,
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {

                    alert(data.Data)
                    location.reload()
                }
            });
        });

        $("#excel_template_new").change(function () {
            var form_data = new FormData();
            form_data.append("excel", $("#excel_template_new")[0].files[0]);
            $.ajax({
                url: '/change_excel_template',
                type: 'POST',
                data: form_data,
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    alert(data.Data)
                    location.reload()
                }
            });
        });

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
