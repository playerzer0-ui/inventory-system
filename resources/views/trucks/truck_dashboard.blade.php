<x-header :title="$title" />

<main>
    @foreach ($orders as $key)
    <div class="card" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title">{{$key["nomor_surat_jalan"]}}</h5>
            <h6 class="card-subtitle mb-2 text-body-secondary">{{$key["orderDate"]}}</h6>
            <a href="{{route('deliver', ['no_sj' => $key["nomor_surat_jalan"]])}}" class="card-link"><button class="btn btn-primary">deliver</button></a>
        </div>
    </div>
    @endforeach
</main>

<x-footer />