<x-header :title="$title" />

<main class="container">
    <h1>Purchase Order</h1>
    <form action="{{route('checkOutPurchase')}}" method="POST" id="orderForm">
        @csrf
        <div id="purchaseOrder"></div>
        <div class="text-start mt-4" id="theButton">
        </div>
    </form>
</main>

<script src="{{asset('js/purchase_order.js')}}" async defer></script>
<x-footer />