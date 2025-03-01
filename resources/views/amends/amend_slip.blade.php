<x-header :title="$title" />

<main class="edit-container">
    <form id="myForm" action="{{route('amend_slip_data')}}" method="post">
        @csrf
        <h1>AMEND SLIP {{$state}}</h1>
        <input type="hidden" id="pageState" name="pageState" value={{$state}}>
        <input name="old_sj" type="hidden" id="old_sj" value="{{$result['nomor_surat_jalan']}}">
        <table>
            <!-- Your form header table here -->
            <tr class="form-header">
                <td>Storage</td>
                <td>:</td>
                <td colspan="2">
                    @if ($state == "out")
                    <select name="storageCode" id="storageCode" readonly>
                        <option value="NON" selected>none</option>
                    </select>
                    @else
                        @if ($state == "in")
                        <select name="storageCode" id="storageCode" onchange="getLPB()" readonly>
                        @else
                        <select name="storageCode" id="storageCode" onchange="getSJT()" readonly> 
                        @endif
                    @foreach ($storages as $key)
                        @if ($key["storageCode"] == $result["storageCode"])
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
                            @if ($key["vendorCode"] == $result["vendorCode"])
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
                            @if ($key["vendorCode"] == $result["vendorCode"])
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
                    <input name="no_lpb_display" type="text" id="no_lpb_display" placeholder="Automatic from the system" value="{{$result['no_LPB']}}" readonly>
                    <input name="no_LPB" type="hidden" value="{{$result['no_LPB']}}" id="no_LPB">
                </td>
                @else
                <td>No SJ</td>
                <td>:</td>
                <td colspan="2"><input name="no_sj" type="text" id="no_sj" placeholder="Automatic from the system" value="{{$result['nomor_surat_jalan']}}" readonly></td>
                @endif
                <td>Order Date</td>
                <td>:</td>
                <td>
                @if ($state == "in")
                <input name="order_date" type="date" id="order_date" onchange="getLPB()" value="{{$result['orderDate']}}" placeholder="fill in" required>
                @elseif($state == "out")
                <input name="order_date" type="date" id="order_date" onchange="getSJ()" value="{{$result['orderDate']}}" placeholder="fill in" required>
                @else
                <input name="order_date" type="date" id="order_date" onchange="getSJT()" value="{{$result['orderDate']}}" placeholder="fill in" required>
                @endif
                </td>
            </tr>
            <tr class="highlight">
                @if ($state == "in")
                <td>No SJ</td>
                <td>:</td>
                <td colspan="2"><input name="no_sj" type="text" id="no_sj" value="{{$result['nomor_surat_jalan']}}" placeholder="fill in" required></td>
                @else
                <td>Truck No</td>
                <td>:</td>
                <td colspan="2"><input name="no_truk" type="text" id="no_truk" value="{{$result['no_truk']}}" placeholder="fill in" required></td>
                @endif
                <td>Purchase Order</td>
                <td>:</td>
                <td><input name="purchase_order" type="text" id="purchase_order" value="{{$result['purchase_order']}}" placeholder="fill in" required></td>
            </tr>
            <tr>
                @if ($state == "in")
                <td>Truck No</td>
                <td>:</td>
                <td colspan="2"><input name="no_truk" type="text" id="no_truk" value="{{$result['no_truk']}}" placeholder="fill in" required></td>
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
                    <th>KD</th>
                    <th>Material</th>
                    <th>QTY</th>
                    <th>UOM</th>
                    <th>Note</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be added here dynamically -->
                @php $count = 1; @endphp
                @foreach($products as $key)
                <tr>
                    <td>{{ $count++ }}</td>
                    <td>
                        <input type="text" name="kd[]" placeholder="Fill in" class="productCode" 
                               oninput="applyAutocomplete(this)" value="{{ $key->productCode }}" required>
                    </td>
                    <td>
                        <input style="width: 300px;" type="text" name="material_display[]" 
                               value="{{ $key->productName }}" readonly>
                        <input type="hidden" name="material[]">
                    </td>
                    <td>
                        <input type="number" name="qty[]" placeholder="Fill in" value="{{ $key->qty }}" required>
                    </td>
                    <td>
                        <input type="text" name="uom[]" placeholder="Fill in" value="{{ $key->uom }}" required>
                    </td>
                    <td>
                        <input type="text" name="note[]" value="{{ $key->note }}" placeholder="">
                    </td>
                    <td>
                        <button class="btn btn-danger" onclick="deleteRow(this)">Delete</button>
                    </td>
                </tr>
                @endforeach                
            </tbody>
        </table>
        <button type="button" class="btn btn-success" onclick="addRow()">Add Row</button>
        <button type="submit" class="btn btn-outline-success">Submit</button>
    </form>
</main>

<script>

</script>
<script src="{{asset('js/slip.js')}}" async defer></script>

<x-footer />
