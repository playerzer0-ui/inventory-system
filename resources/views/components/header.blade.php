<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{$title}}</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.3/themes/smoothness/jquery-ui.css">
        <link rel="stylesheet" href="css/styles.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.3/jquery-ui.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route("dashboard") }}">INVENTORY</a>
                @if (session("userType"))
                    @if (session("userType") == 1)
                        <a class="disabled yellow-text" aria-disabled="true">ADMIN</a>
                    @else
                        <a class="disabled" aria-disabled="true">normal</a>
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
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route("dashboard") }}"><button class="btn btn-info">storage</button></a>
                    </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{ route("debt") }}"><button class="btn btn-info">debt report</button></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{ route("receivables") }}"><button class="btn btn-info">receiveables report</button></a>
                        </li>
                    <li class="nav-item dropdown btn btn-outline-primary">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            in
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{route("slip", ["state" => "in"])}}">slip in</a></li>
                                <li><a class="dropdown-item" href="{{route("invoice", ["state" => "in"])}}">invoice in</a></li>
                                <li><a class="dropdown-item" href="{{route("payment", ["state" => "in"])}}">payment in</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown btn btn-outline-primary">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            out
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{route("slip", ["state" => "out"])}}">slip out</a></li>
                            <li><a class="dropdown-item" href="{{route("invoice", ["state" => "out"])}}">invoice out</a></li>
                            <li><a class="dropdown-item" href="{{route("payment", ["state" => "out"])}}">payment out</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown btn btn-outline-primary">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            edit
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{route('repack')}}">repack</a></li>
                            <li><a class="dropdown-item" href="{{route('moving')}}">moving</a></li>

                            <li><a class="dropdown-item" href="../controller/index.php?action=show_invoice&state=moving">invoice moving</a></li>
                            <li><a class="dropdown-item" href="../controller/index.php?action=show_payment&state=moving">payment moving</a></li>

                        </ul>
                    </li>
                    <li class="nav-item dropdown btn btn-outline-primary">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            amend
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../controller/index.php?action=show_amends&state=slip">edit slips</a></li>

                                <li><a class="dropdown-item" href="../controller/index.php?action=show_amends&state=invoice">edit invoices</a></li>
                                <li><a class="dropdown-item" href="../controller/index.php?action=show_amends&state=payment">edit payments</a></li>
                                <li><a class="dropdown-item" href="../controller/index.php?action=show_amends&state=repack">edit repacks</a></li>
                                <li><a class="dropdown-item" href="../controller/index.php?action=show_amends&state=moving">edit movings</a></li>

                        </ul>
                    </li>

                        <li class="nav-item dropdown btn btn-outline-warning">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                master
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="../controller/index.php?action=master_read&data=vendor">vendors</a></li>
                                <li><a class="dropdown-item" href="../controller/index.php?action=master_read&data=customer">customers</a></li>
                                <li><a class="dropdown-item" href="../controller/index.php?action=master_read&data=product">products</a></li>
                                <li><a class="dropdown-item" href="../controller/index.php?action=master_read&data=storage">storages</a></li>
                                <li><a class="dropdown-item" href="../controller/index.php?action=master_read&data=users">users</a></li>
                            </ul>
                        </li>


                    <li class="nav-item">
                        <a class="nav-link" href="../controller/index.php?action=getLogs"><button class="btn btn-info">LOGS</button></a>
                    </li>
                    <li class="nav-item">
                        @if (session("userType"))
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
    <div class="msg">
        <p>{{session("msg")}}</p>
    </div>