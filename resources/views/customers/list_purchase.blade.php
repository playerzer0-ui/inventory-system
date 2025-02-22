<x-header :title="$title" />

<main>
    <div class="container text-center">
        @foreach ($purchases as $key)
        <div class="row align-items-start">
            <div class="col">
                <b>{{$key['no_PO']}}</b>
            </div>
            <div class="col">
                <a href="{{route('amend_update', ['state' => "purchase", 'code' => $key["no_PO"]])}}"><button class="btn btn-info">edit</button></a>
                <a href="{{route('amend_delete', ['state' => "purchase", 'code' => $key["no_PO"]])}}"><button class="btn btn-danger">delete</button></a>
            </div>
        </div>
        @endforeach
    </div>
</main>

<x-footer />