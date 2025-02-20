<x-header :title="$title" />

<main>
    <label for="productCode">products:</label>
    <select name="productCode" id="productCode">
        @foreach ($products as $key)
            <option value="{{$key["productCode"]}}">{{$key["productName"]}}({{$key["productCode"]}})</option>
        @endforeach
    </select>
    <label for="period">period:</label>
    <input type="number" name="period" id="period" placeholder="insert period to indicate number of days">
    <button class="btn btn-secondary" onclick="getProductData()">generate</button>

    <canvas id="forecast"></canvas>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="{{asset('js/forecast.js')}}"></script>
<x-footer />