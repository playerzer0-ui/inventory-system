<x-header :title="$title" />

<main class="main-container">
    <form id="myForm" action="{{ route('amend_repack_data') }}" method="post">
        @csrf
        <h1 style="text-align:center;">AMEND REPACK</h1>
        <table class="header-table">
            <tr>
                <td>Storage</td>
                <td>:</td>
                <td>
                    <select name="storageCode" id="storageCode" onchange="getRepackNO()" readonly>
                        @foreach ($storages as $key)
                            <option value="{{ $key['storageCode'] }}" 
                                {{ $key['storageCode'] == $repack['storageCode'] ? 'selected' : '' }}>
                                {{ $key['storageName'] }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>Repack Date</td>
                <td>:</td>
                <td><input name="repack_date" id="repack_date" onchange="getRepackNO()" type="date" value="{{$repack['repack_date']}}" required></td>
            </tr>
            <tr>
                <td>NO. Repack</td>
                <td>:</td>
                <td><input name="no_repack" id="no_repack" type="text" value="{{$repack['no_repack']}}" readonly></td>
            </tr>
        </table>

        <h3>Initial Material</h3>
        <table id="materialstartTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Code</th>
                    <th>Initial Material</th>
                    <th>QTY</th>
                    <th>UOM</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @php 
                $count = 1; 
                @endphp
                @foreach($products as $key)
                    @if($key->product_status == "repack_start")
                        <tr>
                            <td>{{$count++}}</td>
                            <td><input name="kd_start[]" class="productCode" oninput="applyAutocomplete(this)" type="text" placeholder="fill in" values="{{$key->productCode}}" required /></td>
                            <td><input name="material_start[]" type="text" placeholder="Automatic From System" values="{{$key->productName}}" readonly /></td>
                            <td><input name="qty_start[]" type="text" placeholder="fill in" values="{{$key->qty}}" required /></td>
                            <td><input name="uom_start[]" type="text" placeholder="fill in" values="{{$key->uom}}" required /></td>
                            <td><input name="note_start[]" type="text" values="{{$key->note}}" /></td>
                            <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Remove</button></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <p>
            <span class="add-row" onclick="addRow('materialstartTable')"><button type="button" class="btn btn-success">add row</button></span>
        </p>

        <h3>New Material</h3>
        <table id="materialBaruTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Code</th>
                    <th>New Material</th>
                    <th>QTY</th>
                    <th>UOM</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @php 
                $count = 1; 
                @endphp
                @foreach($products as $key)
                    @if($key->product_status == "repack_end")
                        <tr>
                            <td>{{$count++}}</td>
                            <td><input name="kd_end[]" class="productCode" oninput="applyAutocomplete(this)" type="text" placeholder="fill in" values="{{$key->productCode}}" required /></td>
                            <td><input name="material_end[]" type="text" placeholder="Automatic From System" values="{{$key->productName}}" readonly /></td>
                            <td><input name="qty_end[]" type="text" placeholder="fill in" values="{{$key->qty}}" required /></td>
                            <td><input name="uom_end[]" type="text" placeholder="fill in" values="{{$key->uom}}" required /></td>
                            <td><input name="note_end[]" type="text" values="{{$key->note}}" /></td>
                            <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Remove</button></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <p>
            <span class="add-row" onclick="addRow('materialBaruTable')"><button type="button" class="btn btn-success">add row</button></span>
        </p>

        <button type="submit" class="btn btn-outline-success">Submit</button>
    </form>
</main>

<script src="{{ asset('js/repack.js') }}"></script>


<x-footer />