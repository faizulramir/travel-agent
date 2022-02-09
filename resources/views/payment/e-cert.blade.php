<html>
    <head>
        <style>
            * { 
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            /* Create two equal columns that floats next to each other */
            .column {
                float: left;
                width: 50%;
                padding: 10px;
                padding-left: 7%;
                padding-top: 23px;
            }

            .column-second {
                float: left;
                width: 50%;
                padding: 10px;
                padding-left: 31%;
            }

            .column-third {
                float: left;
                width: 28%;
                padding: 10px;
            }

            /* Clear floats after the columns */
            .row:after {
                content: "";
                display: table;
                clear: both;
            }
        </style>
    </head>
    @php
        // $image_path = '/assets/images/template_cert.jpg';
        // dd($url_bg);
    @endphp
    <body style="background-image: url({{ $url_bg }}); background-repeat: no-repeat; background-attachment: fixed;  background-size: cover;">
        <br>
        <br>
        <br>
        <br>
        <div class="row">
            <div class="column" style="width: 85.5% !important; text-align:right; padding-top: 15px; padding-bottom: 13px;">
                <h2 style="color:red;"><b>{{ $cert_number }}</b></h2>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="column-second">
                <p style="padding-top: 10px;font-size: 22px;"><b>{{ strtoupper($orders->upload->ta_name) }}</b></p>
                <p style="padding-top: {{ strlen($orders->name) > 27 ? '25px' : '21px' }}; font-size: {{ strlen($orders->name) > 27 ? '18px' : '22px' }};"><b>{{ strtoupper($orders->name) }}</b></p>
                <p style="padding-top: 21px; font-size: 22px;"><b>{{ strtoupper($orders->passport_no) }}</b></p>
                {{--<p style="padding-top: 28px"><b>{{ $orders->dob ? date('d-m-Y', strtotime($orders->dob)) : '' }}</b></p>--}}
                <p style="padding-top: 21px; font-size: 22px;"><b>{{ $newbirth }}</b></p>
            </div>
        </div>
        <br>
        <br>
        <div class="row">
            <div class="column" style="width: 35% !important; padding-left: 7.1% !important; padding-top:24px;">
                <p style="color:blue;"><b>{{ $plan->description }}</b></p>
            </div>
            <div class="column" style="width: 50% !important; padding-left: 0 !important; padding-top:24px;">
                {{--<p style="color:blue;"><b>({{ $orders->dep_date ? date('d-m-Y', strtotime($orders->dep_date)) : '' }}) TO ({{ $orders->return_date ? date('d-m-Y', strtotime($orders->return_date)) : '' }})</b></p>--}}
                <p style="color:blue;"><b>{{ $duration }}</b></p>
            </div>
        </div>
    </body>
</html>