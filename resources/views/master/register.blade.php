<x-header :title="$title" />

<main class="container d-flex justify-content-center align-items-center vh-100">
    <div class="form-container bg-white p-4 rounded shadow" style="max-width: 400px; max-height: 400px;">
    @if($action == "create")
    <form action="{{route('master_create_data', ['data' => $data])}}" method="post">
        <h1>REGISTER</h1>
    @else
    <form action="{{route('master_update_data', ['data' => $data])}}" method="post">
        <h1>UPDATE</h1>
        <input type="hidden" name="oldCode" value="{{$result["userID"]}}">
    @endif
        @csrf
        <div class="mb-3">
            <label class="form-label">email</label>
            <input type="email" name="input_data[]" value="{{$result['email'] ?? ''}}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="input_data[]" required>
        </div>
        <div class="mb-3">
            <label class="form-label">user type</label>
            <select name="input_data[]" required>
                <option value="0">normal user</option>
                <option value="1">admin user</option>
            </select>
        </div>
        @if($action == "create")
            <button type="submit" class="btn btn-primary">register new user</button>
        @else
            <button type="submit" class="btn btn-primary">update user</button>
        @endif
    </form>
    </div>
</main>

<x-footer />