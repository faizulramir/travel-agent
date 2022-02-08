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
    <body>
        <div class="row">
            <div class="column" style="padding: 0; margin: 0;">
                <h4 class="card-title">INVOICE</h4>
            </div>
            <div class="column" style="padding: 0; margin: 0; text-align: right;">
                <h4 class="card-title" style="color: {{ $user->upload->status == '5' ? 'green' : 'red'}}">{{ $user->upload->status == '5' ? 'PAID' : 'UNPAID'}}</h4>
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
                <p>{{ strtoupper($user->name) }}</p>
            </div>
            <div class="column-second">
            </div>
            <div class="column-third" >
                <p><b>Invoice #</b>{{ $user->invoice }}</p>
                <p><b>Invoice Date</b> {{ date('d-m-Y', strtotime($user->upload->upload_date)) }}</p>
            </div>
        </div>

        {{--
        <hr style="border: none; height: 1px; color: #333; background-color: #333;">
        <div class="row">
            <table>
                <thead>
                    <tr>
                        <th>Quantity</th>
                        <th>Description</th>
                        <th>Unit Price</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tables as $table)
                        <tr>
                            <td>{{ $table->quantity }}</td>
                            <td>{{ strtoupper($table->description) }}</td>
                            <td style="text-align: right !important;">RM {{ $table->unit_price }}</td>
                            <td style="text-align: right !important;">RM {{ $table->amount }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <br>
        <hr style="border: none; height: 1px; color: #333; background-color: #333;">
        <br>
        <div class="row">
            <div class="column">
            </div>
            <div class="column-second">
                <p><b>Total</b></p>
            </div>
            <div class="column-third">
                <p>RM {{ $amount }}</p>
            </div>
        </div>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <p><b>Terms & Conditions</b></p>
        <p>Transfer are to be made payable to</p>
        <p>Company Name : Al Khairi Care Sdn Bhd <br> Account No : 1225 8001 3002 150 <br> Bank : Alliance Islamic Bank Berhad <br> Swift Code : ALSRMYKL</p>
        --}}

        <hr style="border: none; height: 1px; color: #333; background-color: #333;">
        <div class="row">
            <table border="0">
                <thead>
                    <tr>
                        <th width="15%" style="text-align: left !important;">Quantity</th>
                        <th width="45%" style="text-align: left !important;">Description</th>
                        <th width="15%" style="text-align: right !important;">Unit Price</th>
                        <th width="25%" style="text-align: right !important;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tables as $table)
                        <tr>
                            <td>{{ $table->quantity }}</td>
                            <td>{{ strtoupper($table->description) }}</td>
                            <td style="text-align: right !important;">{{ number_format((float)$table->unit_price, 2, '.', ',') }}</td>
                            <td style="text-align: right !important;">{{ number_format((float)$table->amount, 2, '.', ',') }}</td>
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="4" style="padding:0;">
                            <hr style="border: none; height: 1px; color: #333; background-color: #333;">
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="text-align: right !important;"><b>Total</b></td>
                        <td style="text-align: right !important;">RM {{ number_format((float)$amount, 2, '.', ',') }}</td>
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




    