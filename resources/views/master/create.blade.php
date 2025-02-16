<x-header :title="$title" />

<main>
    <h1>create {{$data}}</h1>
    <form action="{{route('master_update_data', ["data" => $data])}}" method="post">
        @csrf
        @foreach($keyNames as $key)
            <label>{{$key}}: </label>
            <input type="text" name="input_data[]">
            <br>
        @endforeach
        <button type="submit" class="btn btn-success">submit</button>
    </form>
</main>

<x-footer />