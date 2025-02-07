<x-header :title="$title" />

<main>
    <h1>amend {{$state}}</h1>
    <input type="hidden" id="state" value="{{$state}}">
    <div class="container text-center">
        @foreach($no_SJs as $key)
            <div class="row align-items-start">
                <div class="col">
                    @if($key["nomor_surat_jalan"] != "-")
                        <b>{{$key["nomor_surat_jalan"]}}</b>
                    @else
                        <b>{{$key["no_moving"]}}</b>
                    @endif
                </div>
                <div class="col">
                    @if($state == "payment")
                        @if($key["nomor_surat_jalan"] != "-")
                            <a href=<?php echo "../controller/index.php?action=amend_update&data=" . $state . "&payment_id=" . $key["payment_id"] . "&code=" . $key["nomor_surat_jalan"]; ?>><button class="btn btn-info">EDIT</button></a>
                            <a href=<?php echo "../controller/index.php?action=master_delete&data=" . $state . "&payment_id=" . $key["payment_id"] . "&code=" . $key["nomor_surat_jalan"]; ?>><button class="btn btn-danger">DELETE</button></a>
                        @else
                            <a href=<?php echo "../controller/index.php?action=amend_update&data=" . $state . "&payment_id=" . $key["payment_id"] . "&code=" . $key["no_moving"]; ?>><button class="btn btn-info">EDIT</button></a>
                            <a href=<?php echo "../controller/index.php?action=master_delete&data=" . $state . "&payment_id=" . $key["payment_id"] . "&code=" . $key["no_moving"]; ?>><button class="btn btn-danger">DELETE</button></a>
                        @endif
                    @else
                        @if($key["nomor_surat_jalan"] != "-")
                            <a href=<?php echo "../controller/index.php?action=amend_update&data=" . $state . "&code=" . $key["nomor_surat_jalan"]; ?>><button class="btn btn-info">EDIT</button></a>
                            <a href=<?php echo "../controller/index.php?action=master_delete&data=" . $state . "&code=" . $key["nomor_surat_jalan"]; ?>><button class="btn btn-danger">DELETE</button></a>
                        @else
                            <a href=<?php echo "../controller/index.php?action=amend_update&data=" . $state . "&code=" . $key["no_moving"]; ?>><button class="btn btn-info">EDIT</button></a>
                            <a href=<?php echo "../controller/index.php?action=master_delete&data=" . $state . "&code=" . $key["no_moving"]; ?>><button class="btn btn-danger">DELETE</button></a>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</main>

<x-footer />