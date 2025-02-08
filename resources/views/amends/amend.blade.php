<x-header :title="$title" />

<main>
    <h1>amend {{$state}}</h1>
    <input type="hidden" id="state" value="{{$state}}">
    <div class="container text-center">
        @foreach($no_SJs as $key)
            <div class="row align-items-start">
                <div class="col">
                    @if ($state == "payment")
                        @if($key["nomor_surat_jalan"] != "-")
                            <b>{{$key["nomor_surat_jalan"]}}   |   payment_id: {{$key["payment_id"]}}</b>
                        @else
                            <b>{{$key["no_moving"]}}   |   payment_id: {{$key["payment_id"]}}</b>
                        @endif
                    @else
                        @if($key["nomor_surat_jalan"] != "-")
                            <b>{{$key["nomor_surat_jalan"]}}</b>
                        @else
                            <b>{{$key["no_moving"]}}</b>
                        @endif
                    @endif
                </div>
                <div class="col">
                    @if($state == "payment")
                        @if($key["nomor_surat_jalan"] != "-")
                            <a href="{{route('amend_update', ['state' => $state, 'payment_id' => $key['payment_id'], 'code' => $key["nomor_surat_jalan"]])}}"><button class="btn btn-info">EDIT</button></a>
                            <a href="{{route('master_delete', ['state' => $state, 'payment_id' => $key['payment_id'], 'code' => $key["nomor_surat_jalan"]])}}"><button class="btn btn-danger">DELETE</button></a>
                        @else
                            <a href="{{route('amend_update', ['state' => $state, 'payment_id' => $key['payment_id'], 'code' => $key["no_moving"]])}}"><button class="btn btn-info">EDIT</button></a>
                            <a href="{{route('master_delete', ['state' => $state, 'payment_id' => $key['payment_id'], 'code' => $key["no_moving"]])}}"><button class="btn btn-danger">DELETE</button></a>
                        @endif
                    @else
                        @if($key["nomor_surat_jalan"] != "-")
                            <a href="{{route('amend_update', ['state' => $state, 'code' => $key["nomor_surat_jalan"]])}}"><button class="btn btn-info">EDIT</button></a>
                            <a href="{{route('master_delete', ['state' => $state, 'code' => $key["nomor_surat_jalan"]])}}"><button class="btn btn-danger">DELETE</button></a>
                        @else
                            <a href="{{route('amend_update', ['state' => $state, 'code' => $key["no_moving"]])}}"><button class="btn btn-info">EDIT</button></a>
                            <a href="{{route('master_delete', ['state' => $state, 'code' => $key["no_moving"]])}}"><button class="btn btn-danger">DELETE</button></a>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</main>

<x-footer />