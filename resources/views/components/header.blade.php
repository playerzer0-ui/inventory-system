<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{$title}}</title>
        <meta name="description" content="">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.3/themes/smoothness/jquery-ui.css">
        <link rel="stylesheet" href="css/styles.css">
        <link rel="stylesheet" href="css/receipt.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.3/jquery-ui.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <h5>INVENTORY</h5>
                @if (session("userType") !== null)
                    @if (session("userType") == 1)
                        <a class="disabled yellow-text" aria-disabled="true">ADMIN</a>
                    @elseif(session("userType") == 2)
                        <a class="disabled" aria-disabled="true">{{session('customerCode')}}</a>
                    @elseif(session("userType") == 3)
                        <a class="disabled" aria-disabled="true">{{session('no_truk')}}</a>
                    @else
                        <a class="disabled" aria-disabled="true">supplier</a>
                    @endif
                @endif
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
            @if(session('userType') == 2)
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route("customer_dashboard") }}"><button class="btn btn-secondary">customer dashboard</button></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route("purchase_order") }}"><button class="btn btn-secondary">puchase order</button></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route("list_purchase") }}"><button class="btn btn-secondary">list purchases</button></a>
                    </li>
            @elseif(session('userType') == 3)
            @else
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route("forecast") }}"><button class="btn btn-info">forecast</button></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route("dashboard") }}"><button class="btn btn-info">storage</button></a>
                    </li>
                    @if (session('userType') == 1)
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route("debt") }}"><button class="btn btn-info">debt report</button></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route("receivables") }}"><button class="btn btn-info">receiveables report</button></a>
                    </li>
                    @endif
                    <li class="nav-item dropdown btn btn-outline-primary">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            in
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{route("slip", ["state" => "in"])}}">slip in</a></li>
                            @if (session('userType') == 1)
                            <li><a class="dropdown-item" href="{{route("invoice", ["state" => "in"])}}">invoice in</a></li>
                            <li><a class="dropdown-item" href="{{route("payment", ["state" => "in"])}}">payment in</a></li>
                            @endif
                        </ul>
                    </li>
                    <li class="nav-item dropdown btn btn-outline-primary">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            out
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{route("slip", ["state" => "out"])}}">slip out</a></li>
                            @if (session('userType') == 1)
                            <li><a class="dropdown-item" href="{{route("invoice", ["state" => "out"])}}">invoice out</a></li>
                            <li><a class="dropdown-item" href="{{route("payment", ["state" => "out"])}}">payment out</a></li>
                            @endif
                        </ul>
                    </li>
                    <li class="nav-item dropdown btn btn-outline-primary">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            edit
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{route('repack')}}">repack</a></li>
                            <li><a class="dropdown-item" href="{{route('moving')}}">moving</a></li>
                            @if (session('userType') == 1)
                            <li><a class="dropdown-item" href="{{route("invoice", ["state" => "moving"])}}">invoice moving</a></li>
                            <li><a class="dropdown-item" href="{{route("payment", ["state" => "moving"])}}">payment moving</a></li>
                            @endif
                        </ul>
                    </li>
                    <li class="nav-item dropdown btn btn-outline-primary">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            amend
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{route("amends", ["state" => "slip"])}}">edit slips</a></li>
                            @if (session('userType') == 1)
                            <li><a class="dropdown-item" href="{{route("amends", ["state" => "invoice"])}}">edit invoices</a></li>
                            <li><a class="dropdown-item" href="{{route("amends", ["state" => "payment"])}}">edit payments</a></li>
                            @endif
                            <li><a class="dropdown-item" href="{{route("amends", ["state" => "repack"])}}">edit repacks</a></li>
                            <li><a class="dropdown-item" href="{{route("amends", ["state" => "moving"])}}">edit movings</a></li>
                        </ul>
                    </li>

                    @if (session('userType') == 1)
                    <li class="nav-item dropdown btn btn-outline-warning">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            master
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{route('master_read', ['data' => 'vendor'])}}">vendors</a></li>
                            <li><a class="dropdown-item" href="{{route('master_read', ['data' => 'customer'])}}">customers</a></li>
                            <li><a class="dropdown-item" href="{{route('master_read', ['data' => 'truck'])}}">trucks</a></li>
                            <li><a class="dropdown-item" href="{{route('master_read', ['data' => 'product'])}}">products</a></li>
                            <li><a class="dropdown-item" href="{{route('master_read', ['data' => 'storage'])}}">storages</a></li>
                            <li><a class="dropdown-item" href="{{route('master_read', ['data' => 'user'])}}">users</a></li>
                        </ul>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('excel_logs')}}"><button class="btn btn-info">LOGS</button></a>
                    </li>
            @endif
                    <li class="nav-item">
                        @if (session("email"))
                            <a class="nav-link" href="{{ route("logout") }}"><button class="btn btn-primary">LOGOUT</button></a>
                        @else
                            <a class="nav-link" href="{{ route("home") }}"><button class="btn btn-primary">LOGIN</button></a>                 
                        @endif
                    </li>
                </ul>
            </div>
            </div>
        </div>
    </nav>

    @if (session('msg'))
        <div class="msg" id="msgBox" onclick="hideMsg()">
            <p>{{session("msg")}}</p>
        </div>
    @endif