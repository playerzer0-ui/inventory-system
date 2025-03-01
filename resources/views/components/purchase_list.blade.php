<div>
    @foreach($orders as $key)
        @if($key != "-")
            <button onclick="setPurchaseValue('{{$key}}')">{{$key}}</button>
        @endif
    @endforeach
</div>