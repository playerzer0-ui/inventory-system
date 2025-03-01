<x-header :title="$title" />

<main class="edit-container">
    <form id="myForm" action="{{ route('amend_moving_data') }}" method="post">
        @csrf
        <h1 style="text-align:center;">AMEND MOVING</h1>
        <input name="old_moving" id="old_moving" type="hidden" value="{{$moving['no_moving']}}" readonly>
        <table class="header-table">
            <tr>
                <td>Storage Sender</td>
                <td>:</td>
                <td>
                    <select name="storageCodeSender" id="storageCodeSender" onchange="getMovingNO()" readonly>
                        @foreach ($storages as $key)
                            @if ($key['storageCode'] == $moving["storageCodeSender"])
                                <option value="{{ $key['storageCode'] }}" selected>{{ $key['storageName'] }}</option>
                            @else
                                <option value="{{ $key['storageCode'] }}">{{ $key['storageName'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </td>
                <td>Storage Receiver</td>
                <td>:</td>
                <td>
                    <select name="storageCodeReceiver" id="storageCodeReceiver" readonly>
                        @foreach ($storages as $key)
                            @if ($key['storageCode'] == $moving["storageCodeReceiver"])
                                <option value="{{ $key['storageCode'] }}" selected>{{ $key['storageName'] }}</option>
                            @else
                                <option value="{{ $key['storageCode'] }}">{{ $key['storageName'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>NO. moving</td>
                <td>:</td>
                <td><input name="no_moving" id="no_moving" type="text" value="{{$moving['no_moving']}}" readonly></td>
                <td>Moving Date</td>
                <td>:</td>
                <td><input name="moving_date" id="moving_date" onchange="getMovingNO()" type="date" value="{{$moving['moving_date']}}" required></td>
            </tr>
        </table>

        <table id="materialTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Code</th>
                    <th>Material</th>
                    <th>QTY</th>
                    <th>UOM</th>
                    <th>price/UOM</th>
                    <th>total</th>
                    <th>action</th>
                </tr>
            </thead>
            <tbody>
                @php 
                $count = 1; 
                @endphp

                @foreach($products as $key)
                <tr>
                    <td>{{$count++}}</td>
                    <td><input name="kd[]" class="productCode" oninput="applyAutocomplete(this)" type="text" placeholder="di isi" value="{{ $key->productCode }}" required/></td>
                    <td><input name="productName[]" type="text" placeholder="Automatic from the system" value="{{ $key->productName }}" readonly/></td>
                    <td><input name="qty[]" type="text" placeholder="di isi" oninput="calculateNominal(this)" value="{{ $key->qty }}" required/></td>
                    <td><input name="uom[]" type="text" placeholder="di isi" value="{{ $key->uom }}" required/></td>
                    <td><input name="price_per_uom[]" type="text" placeholder="Automatic from the system" value="{{ $key->price_per_UOM }}" readonly/></td>
                    <td><input type="number" inputmode="numeric" name="nominal[]" placeholder="Automatic from the system" value="{{ (int)$key->qty * (double)$key->price_per_UOM }}" readonly></td>
                    <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Remove</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p>
            <span class="add-row" onclick="addRow('materialTable')"><button type="button" class="btn btn-success">add row</button></span>
            <button type="submit" class="btn btn-outline-success">Submit</button>
        </p>

    </form>
</main>

<script src="{{ asset('js/moving.js') }}"></script>

<x-footer />