<x-header :title="$title" />

<main>
    <div class="container text-center">
        @foreach ($purchases as $key)
        <div class="row align-items-start">
            <div class="col">
                <b>{{$key['no_PO']}}</b>
                @if ($key['status_mode'] == 1)
                    <span>(created)</span>
                @elseif($key['status_mode'] == 2)
                    <span>(pending, delivering)</span>
                @else
                    <span>(delivered and complete)</span>
                @endif
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