<x-header :title="$title" />

<main>
    <div class="container">
        <div class="row">
            @foreach($products as $key)
                <div class="col-md-4 mb-4">
                    <div class="card" style="width: 18rem;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $key['productName'] }}</h5>
                            <p class="card-text">{{ $key['productPrice'] }}</p>
    
                            <div class="d-flex align-items-center mb-3">
                                <button class="btn btn-outline-secondary" onclick="decreaseQuantity('{{ $key['productCode'] }}')">-</button>
                                <input type="number" id="quantity-{{ $key['productCode'] }}" class="form-control mx-2 text-center" value="1" min="1" style="width: 60px;">
                                <button class="btn btn-outline-secondary" onclick="increaseQuantity('{{ $key['productCode'] }}')">+</button>
                            </div>
    
                            <button class="btn btn-primary" onclick="addToCart('{{ $key['productCode'] }}', '{{ $key['productName'] }}', {{ $key['productPrice'] }})">Buy</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>    
</main>

<script src="{{asset('js/customer_dashboard.js')}}" async defer></script>
<x-footer />