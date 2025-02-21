<x-header :title="$title" />

<main>
    <h1>purchase order</h1>
    <form action="" method="POST">
        @csrf
        <label>purchase date</label>
        <input type="date" name="purchaseDate">
        <br>

    </form>
</main>

<x-footer />