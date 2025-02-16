<x-header :title="$title" />

<main>
    <a href="{{route('master_create', ['data' => $data])}}"><button class="btn btn-primary">create new+</button></a>
    <table>
        <tr>
        @foreach($keyNames as $key)
            <th>{{$key}}</th>
        @endforeach
        <th colspan="2">actions</th>
        </tr>
        @foreach($result as $key)
        <tr>
            @foreach($keyNames as $name)
                <td>{{$key[$name]}}</td>
            @endforeach
            <td><a href="{{route('master_update', ['code' => $key[$keyNames[0]], 'data' => $data])}}"><button class="btn btn-info">update</button></a></td>
            <td><a href="{{route('master_delete', ['code' => $key[$keyNames[0]], 'data' => $data])}}"><button class="btn btn-danger">delete</button></a></td>
        </tr>
        @endforeach
    </table>
</main>

<x-footer />