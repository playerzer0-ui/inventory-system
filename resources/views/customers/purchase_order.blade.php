<x-header :title="$title" />

<main>
    <h1>purchase order</h1>
    <form action="{{route('create_purchase')}}" method="POST">
        @csrf
        <label>purchase date</label>
        <input type="date" name="purchaseDate" required>
        <input type="hidden" name="customerCode" value="{{session('customerCode')}}">
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
                <!-- Rows will be added here dynamically -->
            </tbody>
        </table>
        <button type="button" class="btn btn-success" onclick="addRow()">Add Row</button>
        <button type="submit" class="btn btn-outline-success">Submit</button>
    </form>
</main>

<script src="{{asset('js/purchase_order.js')}}" async defer></script>
<x-footer />