<x-header :title="$title" />

<main class="edit-container">
    <form id="myForm" action="{{route('amend_invoice_data')}}" method="post">
        @csrf
        <h1>AMEND INVOICE {{$state}}</h1>
        <input type="hidden" id="pageState" name="pageState" value={{$state}}>
        <table>
            <tr class="form-header">
                @if ($state == "moving")
                <td>Storage Sender</td>
                <td>:</td>
                <td><input type="text" name="storageCodeSender" id="storageCodeSender" placeholder="Automatic from the system" value="{{$result['storageCodeSender']}}" readonly></td>
                <td>Storage Receiver</td>
                <td>:</td>
                <td><input type="text" name="storageCodeReceiver" id="storageCodeReceiver" placeholder="Automatic from the system" value="{{$result['storageCodeReceiver']}}" readonly></td>
                @else
                <td>Storage</td>
                <td>:</td>
                <td colspan="2"><input name="storageCode" type="text" id="storageCode" placeholder="Automatic from the system" value="{{$result['storageCode']}}" readonly></td>
                    @if ($state == "in")
                    <td>Name Vendor</td>
                    <td>:</td>
                    <td colspan="2"><input name="vendorCode" type="text" id="vendorCode" placeholder="Automatic from the system" value="{{$result['vendorCode']}}" readonly></td>
                    @else
                    <td>Name Customer</td>
                    <td>:</td>
                    <td colspan="2"><input name="customerCode" type="text" id="customerCode" placeholder="Automatic from the system" value="{{$result['customerCode']}}" readonly></td>
                    @endif
                @endif
            </tr>
            <tr>
                @if ($state == "moving")
                    <td>NO. moving</td>
                    <td>:</td>
                    <td><input name="no_moving" id="no_moving" type="text" oninput="getMovingDetailsFromMovingNo()" value="{{$result['no_moving']}}" required></td>
                    <td>Moving Date</td>
                    <td>:</td>
                    <td><input name="moving_date" id="moving_date" type="date" value="{{$result['moving_date']}}" readonly></td>
                @else
                    @if ($state == "in")
                        <td>NO. LPB</td>
                        <td>:</td>
                        <td colspan="2"><input name="no_LPB" type="text" id="no_LPB" placeholder="Automatic from the system" value="{{$result['no_LPB']}}" readonly></td>
                        <td>Purchase Order</td>
                        <td>:</td>
                        <td colspan="2"><input name="purchase_order" type="text" id="purchase_order" placeholder="Automatic from the system" value="{{$result['purchase_order']}}" readonly></td>
                    @else
                        <td>No SJ</td>
                        <td>:</td>
                        <td colspan="2"><input name="no_sj_display" type="text" id="no_sj" placeholder="fill in" value="{{$result['nomor_surat_jalan']}}" disabled><input type="hidden" name="no_sj" value="{{$result['nomor_surat_jalan']}}"></td>
                        <td>Address</td>
                        <td>:</td>
                        <td colspan="2"><input name="customerAddress" type="text" id="customerAddress" placeholder="Automatic from the system" value="{{$result['customerAddress']}}" readonly></td>
                    @endif
                @endif
            </tr>
            <tr class="highlight">
                @if ($state == "moving")
                    <td>No Invoice</td>
                    <td>:</td>
                    <td><input name="no_invoice" type="text" id="no_invoice" placeholder="Automatic from the system" value="{{$invoice['no_invoice']}}" readonly></td>
                    <td>Invoice Date</td>
                    <td>:</td>
                    <td colspan="2"><input name="invoice_date" type="date" id="invoice_date" placeholder="fill in" oninput="generateNoInvoice()" value="{{$invoice['invoice_date']}}" required></td>
                @else
                    @if ($state == "in")
                        <td>No SJ</td>
                        <td>:</td>
                        <td colspan="2"><input name="no_sj_display" type="text" id="no_sj" placeholder="fill in" value="{{$result['nomor_surat_jalan']}}" disabled><input type="hidden" name="no_sj" value="{{$result['nomor_surat_jalan']}}"></td>
                        <td>Invoice Date</td>
                        <td>:</td>
                        <td colspan="2"><input name="invoice_date" type="date" id="invoice_date" placeholder="fill in" value="{{$invoice['invoice_date']}}" required></td>
                    @else
                        <td>No Invoice</td>
                        <td>:</td>
                        <td colspan="2"><input name="no_invoice" type="text" id="no_invoice" placeholder="Automatic from the system" value="{{$invoice['no_invoice']}}" readonly></td>
                        <td>NPWP</td>
                        <td>:</td>
                        <td colspan="2"><input name="npwp" type="text" id="npwp" placeholder="Automatic from the system" value="{{$result['customerNPWP']}}" readonly></td>
                    @endif
                @endif
            </tr>
            @if ($state != "moving")
            <tr>
                @if ($state == "in")
                    <td>Truck No</td>
                    <td>:</td>
                    <td colspan="2"><input name="no_truk" type="text" id="no_truk" placeholder="Automatic from the system" value="{{$result['no_truk_in']}}" readonly></td>
                    <td>No Invoice</td>
                    <td>:</td>
                    <td colspan="2"><input name="no_invoice" type="text" id="no_invoice" placeholder="fill in" value="{{$invoice['no_invoice']}}" required></td>
                @else
                    <td>Invoice Date</td>
                    <td>:</td>
                    <td colspan="2"><input name="invoice_date" type="date" id="invoice_date" placeholder="fill in" oninput="generateNoInvoice()" value="{{$invoice['no_invoice']}}" required></td>
                    <td colspan="4"></td>
                @endif
            </tr>
            @endif
        </table>

        <table id="productTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Code</th>
                    <th>Material</th>
                    <th>QTY</th>
                    <th>UOM</th>
                    <th>price/UOM</th>
                    <th>total</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be added here dynamically -->
                @php 
                $count = 1; 
                $totalNominal = 0;
                @endphp

                @foreach($products as $key)
                <tr>
                    <td>{{ $count++ }}</td>
                    <td>
                        <input type="text" name="kd[]" value="{{ $key->productCode }}" class="productCode" readonly>
                    </td>
                    <td>
                        <input style="width: 300px;" value="{{ $key->productName }}" type="text" name="material_display[]" readonly>
                        <input type="hidden" value="{{ $key->productName }}" name="material[]">
                    </td>
                    <td>
                        <input type="number" value="{{ $key->qty }}" name="qty[]" readonly>
                    </td>
                    <td>
                        <input type="text" value="{{ $key->uom }}" name="uom[]" readonly>
                    </td>
                    <td>
                        <input type="number" value="{{ $key->price_per_UOM }}" inputmode="numeric" 
                            name="price_per_uom[]" placeholder="Fill in" oninput="calculateNominal(this)" required>
                    </td>
                    <td>
                        <input type="text" name="nominal[]" placeholder="Automatic from the system" 
                            value="{{ (int)$key->qty * (double)$key->price_per_UOM }}" readonly>
                    </td>
                </tr>
                @php
                    $totalNominal += (int)$key->qty * (double)$key->price_per_UOM;
                @endphp
                @endforeach
            </tbody>
        </table>

        <table id="accountTable">
            <tr>
                <th>Factor Code: </th>
                <td><input type="text" name="no_faktur" id="no_faktur" placeholder="fill in" value="{{$invoice['no_faktur']}}" required></td>
                <th>Total value of goods: </th>
                <td><input type="number" inputmode="numeric" name="totalNominal" id="totalNominal" placeholder="Automatic from the system" disabled></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>Tax(%): <input type="number" name="tax" id="tax" value="{{$invoice['tax']}}" oninput="calculateTotalNominal()"></td>
                <td><input type="number" inputmode="numeric" name="taxPPN" id="taxPPN" placeholder="Automatic from the system" disabled></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>Amount Paid: </td>
                <td><input type="number" inputmode="numeric" name="amount_paid" id="amount_paid" placeholder="Automatic from the system" disabled></td>
            </tr>
        </table>
        <button type="submit" class="btn btn-outline-success">Submit</button>
    </form>
</main>

<script src="{{asset('js/invoice.js')}}" async defer></script>

<x-footer />