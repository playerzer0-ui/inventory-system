<x-header :title="$title" />

<main>
    <h1>Update {{$data}}</h1>
    <form action="{{route('master_update_data', ["data" => $data])}}" method="post">
        @csrf
        <input type="hidden" name="oldCode" value="{{$result[$keyNames[0]]}}">
        @foreach($keyNames as $key)
            <label>{{$key}}: </label>
            @if (stripos($key, 'password') !== false)
            <input type="text" name="input_data[]">
            @else
            <input type="text" name="input_data[]" value="{{$result[$key]}}">
            @endif
            <br>
        @endforeach
        <button type="submit" class="btn btn-success">Update</button>
    </form>
</main>


<x-footer />