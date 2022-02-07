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
        <p>From</p>
        <p>AL KHAIRI CARE SDN. BHD. (1415158-U) <br>B-28-1 Star Avenue Commercial Centre, Jalan Zuhal U5/179, Section U5, <br> 40150 Shah Alam, Selangor. <br>  https://alkhairicare.com/ <br> alkhairiecare@gmail.com</p>
        <br>
        <div class="row">
            <div class="column">
                <p><b>Bill To</b></p>
                <p>{{ strtoupper($user->name) }}</p>
            </div>
            <div class="column-second">
            </div>
            <div class="column-third" >
                <p><b>Invoice #</b>{{ $user->invoice }}</p>
                <p><b>Invoice Date</b> {{ date('d-m-Y', strtotime($date_today)) }}</p>
            </div>
        </div>
        <br>
        <br>
        <hr style="border: none; height: 1px; color: #333; background-color: #333;">
        <br>
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
                            <td>RM {{ $table->unit_price }}</td>
                            <td>RM {{ $table->amount }}</td>
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
    </body>
</html>




    