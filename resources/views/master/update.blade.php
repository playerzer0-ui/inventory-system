<x-header :title="$title" />

<main class="container d-flex justify-content-center align-items-center vh-100">
    <div class="form-container bg-white p-4 rounded shadow" style="max-width: 400px; max-height: 400px;">
        <form action="{{route('master_update_data', ["data" => $data])}}" method="post">
        <h1>Update {{$data}}</h1>
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
    </div>
</main>


<x-footer />