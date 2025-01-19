<x-header :title="$title" />

<main class="main-container">
    <form id="myForm" action="{{route("create_slip")}}" method="post">
        @csrf
        <h1>SLIP {{$state}}</h1>
        <input type="hidden" id="pageState" name="pageState" value={{$state}}>
        <table>
            <!-- Your form header table here -->
            <tr class="form-header">
                <td>PT</td>
                <td>:</td>
                <td colspan="2">
                    @if ($state == "out")
                    <select name="storageCode" id="storageCode" readonly>
                        <option value="NON" selected>none</option>
                    </select>
                    @else
                    <select name="storageCode" id="storageCode" onchange="getLPB()" readonly>
                    @foreach ($storages as $key)
                        @if ($key["storageCode"] == "NON")
                        <option value="{{ $key["storageCode"] }}" selected>{{ $key["storageName"] }}</option>
                        @else
                        <option value="{{ $key["storageCode"] }}">{{ $key["storageName"] }}</option>
                        @endif
                    @endforeach
                    </select>
                    @endif
                </td>
                @if ($state == "in")
                <td>Name Vendor</td>
                <td>:</td>
                <td>
                    <input type="hidden" id="status_mode" name="status_mode" value="1">
                    <select name="vendorCode" id="vendorCode">
                        @foreach ($vendors as $key)
                            @if ($key["vendorCode"] == "NON")
                            <option value="{{$key["vendorCode"]}}" selected>{{$key["vendorName"]}}</option>
                            @else
                            <option value="{{$key["vendorCode"]}}">{{$key["vendorName"]}}</option>
                            @endif
                        @endforeach
                    </select>
                </td>
                @else
                <td>Name customer</td>
                <td>:</td>
                <td>
                    <input type="hidden" id="status_mode" name="status_mode" value="2">
                    <select name="customerCode" id="customerCode">
                        @foreach ($customers as $key)
                            @if ($key["vendorCode"] == "NON")
                            <option value="{{$key["customerCode"]}}" selected>{{$key["customerName"]}}</option>
                            @else
                            <option value="{{$key["customerCode"]}}">{{$key["customerName"]}}</option>
                            @endif
                        @endforeach
                    </select>
                </td> 
                @endif
            </tr>
            <tr>
                @if ($state == "in")
                <td>NO. LPB</td>
                <td>:</td>
                <td colspan="2">
                    <input name="no_lpb_display" type="text" id="no_lpb_display" placeholder="Automatic from the system" readonly>
                    <input name="no_LPB" type="hidden" id="no_LPB">
                </td>
                @else
                <td>No SJ</td>
                <td>:</td>
                <td colspan="2"><input name="no_sj" type="text" id="no_sj" placeholder="Automatic from the system" readonly></td>
                @endif
                <td>Order Date</td>
                <td>:</td>
                <td>
                @if ($state == "in")
                <input name="order_date" type="date" id="order_date" onchange="getLPB()" placeholder="fill in" required>
                @else
                <input name="order_date" type="date" id="order_date" onchange="getSJ()" placeholder="fill in" required>
                @endif
                </td>
            </tr>
            <tr class="highlight">
                @if ($state == "in")
                <td>No SJ</td>
                <td>:</td>
                <td colspan="2"><input name="no_sj" type="text" id="no_sj" placeholder="fill in" required></td>
                @else
                <td>Truck No</td>
                <td>:</td>
                <td colspan="2"><input name="no_truk" type="text" id="no_truk" placeholder="fill in" required></td>
                @endif
                <td>Purchase Order</td>
                <td>:</td>
                <td><input name="purchase_order" type="text" id="purchase_order" placeholder="fill in" required></td>
            </tr>
            <tr>
                @if ($state == "in")
                <td>Truck No</td>
                <td>:</td>
                <td colspan="2"><input name="no_truk" type="text" id="no_truk" placeholder="fill in" required></td>
                <td colspan="3"></td>
                @else
                <td></td>
                <td></td>
                <td colspan="2"></td>
                <td colspan="3"></td>
                @endif
            </tr>
        </table>

        <table id="productTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Code</th>
                    <th>Material</th>
                    <th>QTY</th>
                    <th>UOM</th>
                    <th>Note</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be added here dynamically -->
            </tbody>
        </table>
        <button type="button" class="btn btn-success" onclick="addRow()">Add Row</button>
        <button type="submit" class="btn btn-outline-success">Submit</button>
    </form>
</main>

<script src="{{asset('js/slip.js')}}" async defer></script>

<x-footer />