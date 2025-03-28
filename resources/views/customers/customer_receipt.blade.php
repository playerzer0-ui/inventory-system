<div class="no-print">
    <x-header :title="$title" />
</div>

<main class="container d-flex justify-content-center align-items-center">
    <div class="form-container bg-white p-4 rounded shadow" style="max-width: 500px;">
        <div class="container">
            <div class="row align-items-start justify-content-between">
                <div class="col">
                    <b class="purchase-order">{{$no_PO}}</b> <br>
                    <span class="purchase-text">Date: {{$purchase_date}}</span> <br>
                    <span class="purchase-text">Email: {{session("email")}}</span>
                </div>
                <div class="col-auto"> <!-- Use col-auto to fit the icon's width -->
                    <ion-icon name="print-outline" class="print-icon" onclick="printReceipt()"></ion-icon>
                </div>
            </div>

            <table class="receipt-table">
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Nominal</th>
                </tr>
                @php
                    $total = 0;
                @endphp
                @foreach ($products as $key)
                <tr>
                    <td>{{$key->productName}}</td>
                    <td>{{$key->price_per_UOM}}</td>
                    <td>{{$key->qty}}</td>
                    <td>{{( (double)$key->price_per_UOM * (int)$key->qty )}}</td>
                </tr>
                @php
                    $total += (double)$key->price_per_UOM * (int)$key->qty;
                @endphp
                @endforeach
                <tr class="total-row">
                    <td></td>
                    <td></td>
                    <td>Total:</td>
                    <td>{{$total}}</td>
                </tr>
            </table>

            <p class="purchase-text">-Your order is processed and will be expected to be delivered, thank you for your purchase-</p>
        </div>
    </div>
</main>

<script>
    function printReceipt() {
        window.print(); // Open the browser's print dialog
    }
    localStorage.removeItem("cart");
    localStorage.clear();
</script>
<x-footer class="no-print"/>