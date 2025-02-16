<x-header :title="$title" />

<main class="main-container">
    <div class="form-container bg-white">
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
            <input type="email" name="input_data[]" class="form-control" value="{{$result['email'] ?? ''}}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="input_data[]" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">user type</label>
            <select name="input_data[]" class="form-select" required>
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