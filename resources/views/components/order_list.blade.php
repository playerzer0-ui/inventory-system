<div>
    @foreach($orders as $key)
        @if($key != "-")
            <button onclick="setInputValue('{{$key}}')">{{$key}}</button>
        @endif
    @endforeach
</div>