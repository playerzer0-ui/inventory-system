<x-header :title="$title" />

<main class="edit-container">
    <div class="form-container bg-white">
    <form action="{{ route("login") }}" method="post">
        @csrf
        <h1>LOGIN</h1>
        <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" id="exampleInputPassword1">
        </div>
        <button type="submit" class="btn btn-primary">login</button>
    </form>
    <a href="{{ route('show_customer_login') }}"><button type="submit" class="btn btn-secondary">I am a customer</button></a>
    <a href="{{ route('show_truck_login') }}"><button type="submit" class="btn btn-secondary">I am a truck</button></a>
    </div>
</main>

<x-footer />