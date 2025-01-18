<x-header :title="$title" />

<main class="main-container">
    <form id="myForm" action="{{ url('../controller/index.php?action=create_repack') }}" method="post">
        <h1 style="text-align:center;">SLIP REPACK BARANG</h1>
        <input type="hidden" id="pageState" name="pageState" value="{{ $pageState }}">
        <table class="header-table">
            <tr>
                <td>PT</td>
                <td>:</td>
                <td>
                    <select name="storageCode" id="storageCode" onchange="getRepackNO()" readonly>
                        @foreach (getAllStorages() as $key)
                            <option value="{{ $key['storageCode'] }}" 
                                {{ $key['storageCode'] == 'NON' ? 'selected' : '' }}>
                                {{ $key['storageName'] }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>Tgl Repack</td>
                <td>:</td>
                <td><input name="repack_date" id="repack_date" onchange="getRepackNO()" type="date" required></td>
            </tr>
            <tr>
                <td>NO. Repack</td>
                <td>:</td>
                <td><input name="no_repack" id="no_repack" type="text" readonly></td>
            </tr>
        </table>

        <h3>Material Awal</h3>
        <table id="materialAwalTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>KD</th>
                    <th>Material Awal</th>
                    <th>QTY</th>
                    <th>UOM</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td><input name="kd_awal[]" class="productCode" oninput="applyAutocomplete(this)" type="text" placeholder="di isi" required /></td>
                    <td><input name="material_awal[]" type="text" placeholder="Otomatis" readonly /></td>
                    <td><input name="qty_awal[]" type="text" placeholder="di isi" required /></td>
                    <td><input name="uom_awal[]" type="text" placeholder="di isi" required /></td>
                    <td><input name="note_awal[]" type="text" /></td>
                    <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Remove</button></td>
                </tr>
            </tbody>
        </table>
        <p>
            <span class="add-row" onclick="addRow('materialAwalTable')">Add Row</span>
        </p>

        <h3>Material Baru</h3>
        <table id="materialBaruTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>KD</th>
                    <th>Material Baru</th>
                    <th>QTY</th>
                    <th>UOM</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td><input name="kd_akhir[]" class="productCode" oninput="applyAutocomplete(this)" type="text" placeholder="di isi" required /></td>
                    <td><input name="material_akhir[]" type="text" placeholder="Otomatis" readonly /></td>
                    <td><input name="qty_akhir[]" type="text" placeholder="di isi" required /></td>
                    <td><input name="uom_akhir[]" type="text" placeholder="di isi" required /></td>
                    <td><input name="note_akhir[]" type="text" /></td>
                    <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Remove</button></td>
                </tr>
            </tbody>
        </table>
        <p>
            <span class="add-row" onclick="addRow('materialBaruTable')">Add Row</span>
        </p>

        <button type="submit" class="btn btn-outline-success">Submit</button>
    </form>
</main>

<script>
    window.onload = function() {
        getRepackNO();
    };
</script>
<script src="{{ asset('../js/repack.js') }}"></script>


<x-footer />