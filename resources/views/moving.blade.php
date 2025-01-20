<x-header :title="$title" />

<main class="main-container">
    <form id="myForm" action="{{ url('controller/index.php?action=create_moving') }}" method="post">
        <h1 style="text-align:center;">MOVING</h1>
        <input type="hidden" id="pageState" name="pageState" value="{{ $pageState }}">
        <table class="header-table">
            <tr>
                <td>Storage Sender</td>
                <td>:</td>
                <td>
                    <select name="storageCodeSender" id="storageCodeSender" onchange="getMovingNO()" readonly>
                        @foreach ($storages as $key)
                            @if ($key['storageCode'] == 'NON')
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
                            @if ($key['storageCode'] == 'NON')
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
                <td><input name="no_moving" id="no_moving" type="text" readonly></td>
                <td>Moving Date</td>
                <td>:</td>
                <td><input name="moving_date" id="moving_date" onchange="getMovingNO()" type="date" required></td>
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
                <tr>
                    <td>1</td>
                    <td><input name="kd[]" class="productCode" oninput="applyAutocomplete(this)" type="text" placeholder="di isi" required/></td>
                    <td><input name="productName[]" type="text" placeholder="Otomatis" readonly/></td>
                    <td><input name="qty[]" type="text" placeholder="di isi" oninput="calculateNominal(this)" required/></td>
                    <td><input name="uom[]" type="text" placeholder="di isi" required/></td>
                    <td><input name="price_per_uom[]" type="text" placeholder="otomatis" readonly/></td>
                    <td><input type="number" inputmode="numeric" name="nominal[]" placeholder="Otomatis" readonly></td>
                    <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Remove</button></td>
                </tr>
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