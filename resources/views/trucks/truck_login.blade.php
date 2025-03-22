<x-header :title="$title" />

<main class="container d-flex justify-content-center align-items-center vh-100">
    <div class="form-container bg-white p-4 rounded shadow" style="max-width: 400px; max-height: 400px;">
    <form action="{{ route("truck_login") }}" method="post">
        @csrf
        <h1>TRUCK LOGIN</h1>
        <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" id="exampleInputPassword1">
        </div>
        <button type="submit" class="btn btn-primary">login</button>
        <a href="{{ route('show_customer_login') }}" class="btn btn-secondary">I am a customer</a>
        <a href="{{ route('home') }}" class="btn btn-secondary">I am a supplier</a>
    </form>
    </div>
</main>

<x-footer />