<x-header :title="$title" />

<main>
    <h1>CONFIRM DELETE <span style="color: red;">{{$code}}</span>?</h1>
    @if($data == "slip" || $data == "invoice" || $data == "payment" || $data == "repack" || $data == "moving")
    <form action="../controller/index.php?action=amend_delete_data" method="post">
    @else
    <form action="../controller/index.php?action=master_delete_data" method="post">
    @endif
        <input type="hidden" name="data" value="<?php echo htmlspecialchars($data); ?>">
        <input type="hidden" name="code" value="<?php echo htmlspecialchars($code); ?>">
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