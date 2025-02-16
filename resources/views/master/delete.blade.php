<x-header :title="$title" />

<main>
    <h1>CONFIRM DELETE <span style="color: red;">{{$code}}</span>?</h1>
    @if($data == "slip" || $data == "invoice" || $data == "payment" || $data == "repack" || $data == "moving")
    <form action="{{route('amend_delete_data', ["data" => $data, "code" => $code])}}" method="post">
    @else
    <form action="{{route('master_delete_data', ["data" => $data, "code" => $code])}}" method="post">
        @csrf
    @endif
        @if($data == "slip")
            <p>You are about to delete a slip, and by default all records linked to the slip will be gone (invoices, payments), are you sure you want to delete this slip?</p>
        @elseif($data == "invoice" || $data == "payment" || $data == "repack" || $data == "moving")
            <p>You are about to delete a record, the record deleted might affect other records, are you sure you want to delete this record?</p>
        @else
            <p>this data resource will no longer exist on the master table, if there are any orders linked to this data, it won't delete and send an error instead</p>
        @endif
        <button type="submit" class="btn btn-danger">DELETE FOREVER</button>
    </form>
</main>

<x-footer />