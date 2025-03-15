document.addEventListener('DOMContentLoaded', function () {
    let success = document.getElementById("success").value;
    if(success !== null){
        localStorage.removeItem("cart");
        localStorage.clear();
    }
});

function decreaseQuantity(productCode) {
    const input = document.getElementById(`quantity-${productCode}`);
    let value = parseInt(input.value);
    if (value > 1) {
        input.value = value - 1;
    }
}

function increaseQuantity(productCode) {
    const input = document.getElementById(`quantity-${productCode}`);
    input.value = parseInt(input.value) + 1;
}

function addToCart(productCode, productName, productPrice) {
    let cart = JSON.parse(localStorage.getItem('cart')) || {};

    if (cart[productCode]) {
        cart[productCode].quantity += parseInt(document.getElementById(`quantity-${productCode}`).value);
    } else {
        cart[productCode] = {
            productName: productName,
            productPrice: productPrice,
            quantity: parseInt(document.getElementById(`quantity-${productCode}`).value)
        };
    }

    localStorage.setItem('cart', JSON.stringify(cart));

    alert(`Added to cart:\nProduct: ${productName}\nQuantity: ${cart[productCode].quantity}`);
}

