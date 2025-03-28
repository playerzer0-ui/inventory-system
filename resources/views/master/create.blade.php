<x-header :title="$title" />

<main class="container d-flex justify-content-center align-items-center vh-100">
    <div class="form-container bg-white p-4 rounded shadow" style="max-width: 400px; max-height: 400px;">
        <form action="{{route('master_create_data', ["data" => $data])}}" method="post">
        <h1>create {{$data}}</h1>
        @csrf
        @foreach($keyNames as $key)
            <label>{{$key}}: </label>
            <input type="text" name="input_data[]">
            <br>
        @endforeach
        <button type="submit" class="btn btn-success">submit</button>
        </form>
    </div>
</main>

<x-footer />