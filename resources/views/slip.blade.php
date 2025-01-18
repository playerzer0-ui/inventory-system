<x-header :title="$title" />

<main class="main-container">
    <form id="myForm" action="../controller/index.php?action=create_slip" method="post">
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
                    <input name="no_lpb_display" type="text" id="no_lpb_display" placeholder="automatic" readonly>
                    <input name="no_LPB" type="hidden" id="no_LPB">
                </td>
                @else
                <td>No SJ</td>
                <td>:</td>
                <td colspan="2"><input name="no_sj" type="text" id="no_sj" placeholder="di isi" readonly></td>
                @endif
                <td>Order Date</td>
                <td>:</td>
                <td>
                @if ($state == "in")
                <input name="order_date" type="date" id="tgl_penerimaan" onchange="getLPB()" placeholder="di isi" required>
                @else
                <input name="order_date" type="date" id="tgl_penerimaan" onchange="getSJ()" placeholder="di isi" required>
                @endif
                </td>
            </tr>
            <tr class="highlight">
                @if ($state == "in")
                <td>No SJ</td>
                <td>:</td>
                <td colspan="2"><input name="no_sj" type="text" id="no_sj" placeholder="di isi" required></td>
                @else
                <td>Truck No</td>
                <td>:</td>
                <td colspan="2"><input name="no_truk" type="text" id="no_truk" placeholder="di isi" required></td>
                @endif
                <td>Purchase Order</td>
                <td>:</td>
                <td><input name="purchase_order" type="text" id="purchase_order" placeholder="di isi" required></td>
            </tr>
            <tr>
                @if ($state == "in")
                <td>Truck No</td>
                <td>:</td>
                <td colspan="2"><input name="no_truk" type="text" id="no_truk" placeholder="di isi" required></td>
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

{{-- <script>
    <?php if($pageState == "in"){ ?>
    window.onload = function() {
        getLPB();
    };
    <?php } else if($pageState == "out") { ?>
    window.onload = function() {
        getSJ();
    };
    <?php } else { ?>
    window.onload = function() {
        getSJT();
    };
    <?php } ?>
</script>
<script src="../js/index.js" async defer></script> --}}

<x-footer />