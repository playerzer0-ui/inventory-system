<x-header :title="$title" />

<main>
    <h1>Product price list</h1>
    <table>
        <tr>
            <th>productCode</th>
            <th>productName</th>
            <th>productPrice</th>
        </tr>
        @foreach ($products as $key)
            <tr>
                <td>{{$key['productCode']}}</td>
                <td>{{$key['productName']}}</td>
                <td>{{$key['productPrice']}}</td>
            </tr>
        @endforeach
    </table>
</main>

<x-footer />