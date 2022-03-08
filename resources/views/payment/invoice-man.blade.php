<html>
    <head>
        <title>{{ $inv_no }}</title>
        <style>
            * {
                box-sizing: border-box;
            }

            /* Create two equal columns that floats next to each other */
            .column {
                float: left;
                width: 50%;
                padding: 10px;
            }

            .column-second {
                float: left;
                width: 20%;
                padding: 10px;
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
    
            table {
                table-layout: fixed;
                width: 100%; 
                text-align: left !important;
            }

            td,th { padding: .5em 1em;  vertical-align: top; }
        </style>
    </head>
    @php
        // $image_path = '/assets/images/template_cert.jpg';
        // // dd($url_bg);
    @endphp
    <body>

        <div class="row">
            <div class="column" style="padding: 0; margin: 0;">
                <h4 class="card-title">INVOICE</h4>
            </div>
            <div class="column" style="padding: 0; margin: 0; text-align: right;">
                <h4 class="card-title" style="color: {{ $filestatus == '1' ? 'green' : 'red' }}">{{ $filestatus == '1' ? $inv_status : 'DRAFT' }}</h4>
            </div>
        </div>

        <div class="row">
            <div class="column" style="padding: 0; margin: 0;">
                <p><b>From</b></p>
                <p>AL KHAIRI CARE SDN. BHD. (1415158-U) <br>B-28-1 Star Avenue Commercial Centre, <br>Jalan Zuhal U5/179, Section U5, <br>40150 Shah Alam, Selangor. <br>  https://alkhairicare.com/ <br> alkhairiecare@gmail.com</p>
            </div>
            <div class="column" style="padding: 0; margin: 0; text-align: center;">
                <br><br>
                <img src="assets/images/Logo-Al-Khairi-Care.png" alt="" height="50">
                @if ($filestatus != '1')
                <img src="assets/images/draft.png" alt="" height="200" style="position:absolute;top:120px;left:-150px;z-index:9999;">
                @endif
            </div>
        </div>

        <div class="row">
            <div class="column" style="padding: 0; margin: 0;">
                <p><b>Bill To</b></p>
                <p>{{ strtoupper($inv_company) }}</p>
            </div>
            <div class="column-second">
            </div>
            <div class="column-third" >
                <p><b>Invoice #</b> {{ $inv_no }}</p>
                <p><b>Invoice Date</b> {{ date('Y-m-d', strtotime($inv_date)) }}</p>
            </div>
        </div>

        <hr style="border: none; height: 1px; color: #333; background-color: #333;">
        <div class="row">
            <table border="0">
                <thead>
                    <tr>
                        <th width="15%" style="text-align: left !important;">Quantity</th>
                        <th width="45%" style="text-align: left !important;">Description</th>
                        <th width="18%" style="text-align: right !important;">Unit Price</th>
                        <th width="22%" style="text-align: right !important;">Amount</th>
                    </tr>
                </thead>
                <tbody>

                    @if ($inv_arr)
                        @foreach ($inv_arr as $table)
                            <tr>
                                <td>{{ $table['rowInput1'] }}</td>
                                <td>{!! strtoupper($table['rowInput2']) !!}</td>
                                <td style="text-align: right !important;">{{ number_format((float)$table['rowInput3'], 2, '.', ',') }}</td>
                                <td style="text-align: right !important;">{{ number_format((float)$table['rowInput4'], 2, '.', ',') }}</td>
                            </tr>
                        @endforeach
                    @endif

                    @if ($inv_showtotal)
                    <tr>
                        <td colspan="4" style="padding:0;">
                            <hr style="border: none; height: 1px; color: #333; background-color: #333;">
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="text-align: right !important;"><b>Grand Total</b></td>
                        <td style="text-align: right !important;">RM {{ number_format((float)$inv_total, 2, '.', ',') }}</td>
                    </tr>
                    @endif

                </tbody>
            </table>
        </div>

        
        <hr style="border: none; height: 1px; color: #333; background-color: #333;">
        <p><b>Terms & Conditions</b></p>
        <p>Please make payment via (Online Transfer / Cheque) before departure.</p>
        <p>Company's Account Details : </p>
        <p>Company Name : Al Khairi Care Sdn Bhd <br> Account No : 1225 8001 3002 150 <br> Bank : Alliance Bank </p>
        <p>Should payment has already been made, the slip payment MUST be email to
            finance.alkhairicare@gmail.com or directly sent to our officer.</p>
        <p>IMPORTANT <br> We do NOT ACCEPT CASH PAYMENT, and any POST DATED CHEQUE. For any Dishonoured <br>
            Cheques, RM100.00 will be charged. </p>
        <p>CANCELLATION <br> Any cancellation for Al Khairi Care Plan or Travel Personal Accident will be charge penalty of RM10.00 <br>
            per pax.</p>
        <pre></pre>
    </body>
</html>




    