<x-header :title="$title" />

<main>
    <div>
        <input type="hidden" id="userType" value="{{session('userType')}}">
        <label for="storageCode">storage:</label>
        <select name="storageCode" id="storageCode">
            @foreach ($storages as $key)
                @if($key["storageCode"] == "NON")
                    <option value="{{$key["storageCode"]}}" selected>{{$key["storageName"]}}</option>
                @else
                    <option value="{{$key["storageCode"]}}">{{$key["storageName"]}}</option>
                @endif
            @endforeach
        </select>
        <label for="month">Month:</label>
        <select id="month" name="month">
            <option value="01">January</option>
            <option value="02">February</option>
            <option value="03">March</option>
            <option value="04">April</option>
            <option value="05">May</option>
            <option value="06">June</option>
            <option value="07">July</option>
            <option value="08">August</option>
            <option value="09">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>

        <label for="year">Year:</label>
        <select id="year" name="year">
            <!-- JavaScript will populate the year options -->
        </select>
        <button class="btn btn-secondary" onclick="generateReport()">search</button>
        <div id="excel">
            
        </div>
    </div>
    <div class="table-container">
        @if(session('userType') == 1)
            <table id="reporttable">
            <thead>
                <tr>
                    <th rowspan="3">No</th>
                    <th rowspan="3">Product Code</th>
                    <th rowspan="3">Material</th>
                    <th colspan="3">Initial Balance</th>
                    <th colspan="12">In</th>
                    <th colspan="3">Ready-to-Sell Items</th>
                    <th colspan="12">Out</th>
                    <th colspan="3">Final Balance</th>
                </tr>
                <tr>
                    <th rowspan="2">QTY</th>
                    <th rowspan="2">price/qty</th>
                    <th rowspan="2">total</th>
                    <th colspan="3">Purchase</th>
                    <th colspan="3">Moving</th>
                    <th colspan="3">Repack</th>
                    <th colspan="3">Total In</th>
                    <th rowspan="2">QTY</th>
                    <th rowspan="2">price/qty</th>
                    <th rowspan="2">total</th>
                    <th colspan="3">Sales</th>
                    <th colspan="3">Moving</th>
                    <th colspan="3">Repack</th>
                    <th colspan="3">Total Out</th>
                    <th rowspan="2">QTY</th>
                    <th rowspan="2">price/qty</th>
                    <th rowspan="2">total</th>
                </tr>
                <tr>
                    <th>QTY</th>
                    <th>price/qty</th>
                    <th>total</th>
                    <th>QTY</th>
                    <th>price/qty</th>
                    <th>total</th>
                    <th>QTY</th>
                    <th>price/qty</th>
                    <th>total</th>
                    <th>QTY</th>
                    <th>price/qty</th>
                    <th>total</th>
                    <th>QTY</th>
                    <th>price/qty</th>
                    <th>total</th>
                    <th>QTY</th>
                    <th>price/qty</th>
                    <th>total</th>
                    <th>QTY</th>
                    <th>price/qty</th>
                    <th>total</th>
                    <th>QTY</th>
                    <th>price/qty</th>
                    <th>total</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data rows will be populated here -->
            </tbody>
        </table>
        @else
        <table id="reporttable">
            <thead>
                <tr>
                    <th rowspan="3">No</th>
                    <th rowspan="3">Product Code</th>
                    <th rowspan="3">Material</th>
                    <th colspan="1">Initial Balance</th>
                    <th colspan="4">In</th>
                    <th colspan="1">Ready-to-Sell Items</th>
                    <th colspan="4">Out</th>
                    <th colspan="1">Final Balance</th>
                </tr>
                <tr>
                    <th rowspan="2">QTY</th>
                    <th colspan="1">Purchase</th>
                    <th colspan="1">Moving</th>
                    <th colspan="1">Repack</th>
                    <th colspan="1">Total In</th>
                    <th rowspan="2">QTY</th>
                    <th colspan="1">Sales</th>
                    <th colspan="1">Moving</th>
                    <th colspan="1">Repack</th>
                    <th colspan="1">Total Out</th>
                    <th rowspan="2">QTY</th>
                </tr>
                <tr>
                    <th>QTY</th>
                    <th>QTY</th>
                    <th>QTY</th>
                    <th>QTY</th>
                    <th>QTY</th>
                    <th>QTY</th>
                    <th>QTY</th>
                    <th>QTY</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data rows will be populated here -->
            </tbody>
        </table>
        @endif
    </div>
</main>

<script src="{{asset('js/dashboard.js')}}"></script>

<x-footer />