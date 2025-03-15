<x-header :title="$title" />

<main>
    <h1>Amend Purchase Order: {{ $result['no_PO'] }}</h1>
    <form action="{{route('amend_purchase_data')}}" method="POST" id="amendForm">
        @csrf
        <input type="hidden" name="no_PO" value="{{ $result['no_PO'] }}">
        <input type="hidden" name="customerCode" value="{{ $result['customerCode'] }}">
        <input type="hidden" name="purchaseDate" value="{{ $result['purchaseDate'] }}">
        <div class="mb-4">
            <p><strong>Customer Code:</strong> {{ $result['customerCode'] }}</p>
            <p><strong>Purchase Date:</strong> {{ $result['purchaseDate'] }}</p>
        </div>

        <div id="amendPurchaseOrder">
            @if($products->isEmpty())
                <p>Your purchase order is empty.</p>
            @else
                @foreach($products as $key => $product)
                <div class="row align-items-center mb-3">
                    <input type="hidden" name="kd[]" value="{{ $product->productCode }}">
                    <input type="hidden" name="material[]" value="{{ $product->productName }}">
                    <input type="hidden" name="price_per_uom[]" value="{{ $product->price_per_UOM }}">
                    <div class="col-4">
                        <strong>{{ $product->productName }}</strong>
                    </div>
                    <div class="col-2">
                        {{ $product->price_per_UOM }}
                    </div>
                    <div class="col-4 d-flex align-items-center">
                        @if (!isset($mode))
                        <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity('{{ $product->productCode }}')">-</button>
                        @endif
                        @if (!isset($mode))
                        <input type="number" name="qty[]" id="quantity-{{ $product->productCode }}" class="form-control mx-2 text-center" value="{{ $product->qty }}" min="1" style="width: 60px;">
                        @else
                        <input type="number" name="qty[]" id="quantity-{{ $product->productCode }}" class="form-control mx-2 text-center" value="{{ $product->qty }}" min="1" style="width: 60px;" readonly>
                        @endif
                        @if (!isset($mode))
                        <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity('{{ $product->productCode }}')">+</button>
                        @endif
                    </div>
                    @if (!isset($mode))
                    <div class="col-2 text-end">
                        <button type="button" class="btn btn-danger" onclick="removeItem('{{ $product->productCode }}')">Delete</button>
                    </div>
                    @endif
                </div>
                @endforeach

                <div class="mt-4 text-end fw-bold" id="grandTotal">
                    Grand Total: {{ $products->sum(fn($p) => $p->price_per_UOM * $p->qty) }}
                </div>
                <input type="hidden" name="grand_total" id="grand_total" value="{{ $products->sum(fn($p) => $p->price_per_UOM * $p->qty) }}">
                <input type="hidden" name="oldTotal" value="{{ $products->sum(fn($p) => $p->price_per_UOM * $p->qty) }}" id="oldTotal">
            @endif
        </div>

        <div class="text-end mt-4">
            @if (!isset($mode))
                <button type="submit" class="btn btn-success">Save Changes</button>
            @endif
        </div>
    </form>
</main>

<script src="{{asset('js/amend_purchase.js')}}" async defer></script>
<x-footer />