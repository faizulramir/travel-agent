<html>
    <head>
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

            td,th {padding: .5em 1em;}
        </style>
    </head>
    @php
        // $image_path = '/assets/images/template_cert.jpg';
        // // dd($url_bg);
    @endphp
    <body >

        <div class="row">
            <div class="column" style="padding: 0; margin: 0;">
                <h4 class="card-title">INVOICE</h4>
            </div>
            <div class="column" style="padding: 0; margin: 0; text-align: right;">
                <h4 class="card-title" style="color: {{ $files->status == '5' ? 'green' : 'red'}}">{{ $files->status == '5' ? 'PAID' : 'UNPAID'}}</h4>
            </div>
        </div>

        <div class="row">
            <div class="column" style="padding: 0; margin: 0;">
                <p><b>From</b></p>
                <p>AL KHAIRI CARE SDN. BHD. (1415158-U) <br>B-28-1 Star Avenue Commercial Centre, <br>Jalan Zuhal U5/179, Section U5, <br>40150 Shah Alam, Selangor. <br>  https://alkhairicare.com/ <br> alkhairiecare@gmail.com</p>
            </div>
            <div class="column" style="padding: 0; margin: 0; text-align: center;">
                <br><br>
                <img src="assets/images/akc-logo.png" alt="" height="40">
            </div>
        </div>

        <div class="row">
            <div class="column" style="padding: 0; margin: 0;">
                <p><b>Bill To</b></p>
                <p>{{ strtoupper($files->user->name) }}</p>
            </div>
            <div class="column-second">
            </div>
            <div class="column-third" >
                <p><b>Invoice #</b> {{ $invoice_num }}</p>
                <p><b>Invoice Date</b> {{ date('d-m-Y', strtotime($date_today)) }}</p>
            </div>
        </div>

        <hr style="border: none; height: 1px; color: #333; background-color: #333;">
        <div class="row">
            <table border="0">
                <thead>
                    <tr>
                        <th width="15%">Quantity</th>
                        <th width="45%">Description</th>
                        <th width="15%"style="text-align: right !important;">Unit Price</th>
                        <th width="25%"style="text-align: right !important;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice_arr as $table)
                        <tr>
                            <td>{{ $table['COUNT'] }}</td>
                            <td>{{ strtoupper($table['PLAN']) }}</td>
                            <td style="text-align: right !important;">{{ number_format((float)$table['PRICE'], 2, '.', ',') }}</td>
                            <td style="text-align: right !important;">{{ number_format((float)$table['COST'], 2, '.', ',') }}</td>
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="4">
                            <hr style="border: none; height: 1px; color: #333; background-color: #333;">
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="text-align: right !important;"><b>Total</b></td>
                        <td style="text-align: right !important;">RM {{ number_format((float)$tot_inv, 2, '.', ',') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <p><b>Terms & Conditions</b></p>
        <p>Transfer are to be made payable to</p>
        <p>Company Name : Al Khairi Care Sdn Bhd <br> Account No : 1225 8001 3002 150 <br> Bank : Alliance Islamic Bank Berhad <br> Swift Code : ALSRMYKL</p>
    </body>
</html>




    