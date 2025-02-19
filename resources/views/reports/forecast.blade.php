<x-header :title="$title" />

<main>
    <label for="productCode">products:</label>
    <select name="productCode" id="productCode">
        @foreach ($products as $key)
            <option value="{{$key["productCode"]}}">{{$key["productName"]}}({{$key["productCode"]}})</option>
        @endforeach
    </select>
    <button class="btn btn-secondary">generate</button>
</main>


<x-footer />