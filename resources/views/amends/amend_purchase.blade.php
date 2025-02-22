<x-header :title="$title" />

<main>
    <h1>purchase order</h1>
    <form action="{{route('amend_purchase_data')}}" method="POST">
        @csrf
        <label>purchase date</label>
        <input type="hidden" name="no_PO" value="{{$result['no_PO']}}">
        <input type="date" name="purchaseDate" value="{{$result['purchaseDate']}}">
        <input type="hidden" name="customerCode" value="{{$result['customerCode']}}">
        <br>

        <table id="productTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Code</th>
                    <th>Material</th>
                    <th>QTY</th>
                    <th>UOM</th>
                    <th>price/UOM</th>
                    <th>nominal</th>
                    <th>note</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php $count = 1; @endphp
                @foreach($products as $key)
                    <td>{{ $count++ }}</td>
                    <td><input type="text" name="kd[]" class="productCode" placeholder="Fill in" oninput="applyAutocomplete(this)" value="{{ $key->productCode }}" required></td>
                    <td><input style="width: 300px;" type="text" name="material_display[]" placeholder="Automatic from system" value="{{ $key->productName }}" readonly><input type="hidden" name="material[]" value="{{ $key->productName }}"></td>
                    <td><input type="number" name="qty[]" oninput="calculateNominal(this)" placeholder="Fill in" value="{{ $key->qty }}" required></td>
                    <td><input type="text" name="uom[]" placeholder="Fill in" value="{{ $key->uom }}" required></td>
                    <td><input type="number" inputmode="numeric" name="price_per_uom[]" value="{{ $key->price_per_UOM }}" placeholder="Automatic from system" readonly></td>
                    <td><input type="text" name="nominal[]" oninput="calculateNominal(this)" placeholder="Automatic from system" value="{{ (int)$key->qty * (double)$key->price_per_UOM }}" readonly></td>
                    <td><input type="text" name="note[]" value="{{ $key->note }}" placeholder=""></td>
                    <td><button class="btn btn-danger" onclick="deleteRow(this)">Delete</button></td>
                @endforeach
            </tbody>
        </table>
        <button type="button" class="btn btn-success" onclick="addRow()">Add Row</button>
        <button type="submit" class="btn btn-outline-success">Submit</button>
    </form>
</main>

<script src="{{asset('js/purchase_order.js')}}" async defer></script>
<x-footer />