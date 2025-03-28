// Retrieve cart data from localStorage
const cart = JSON.parse(localStorage.getItem('cart')) || {};
const purchaseOrderDiv = document.getElementById('purchaseOrder');
let grandTotal = 0;


if (Object.keys(cart).length === 0) {
    purchaseOrderDiv.innerHTML = '<p>Your cart is empty.</p>';
} else {
    Object.keys(cart).forEach((productCode, index) => {
        const item = cart[productCode];
        const totalPrice = item.productPrice * item.quantity;
        grandTotal += totalPrice; // Dynamically calculate grand total

        const rowDiv = document.createElement('div');
        rowDiv.classList.add('row', 'align-items-center', 'mb-3');
        rowDiv.innerHTML = `
            <input type="hidden" name="kd[]" value="${productCode}">
            <input type="hidden" name="price_per_uom[]" value="${item.productPrice}">
            <div class="col-4">
                <strong>${item.productName}</strong>
            </div>
            <div class="col-2">
                ${item.productPrice}
            </div>
            <div class="col-4 d-flex align-items-center">
                <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity('${productCode}')">-</button>
                <input type="number" name="qty[]" id="quantity-${productCode}" class="form-control mx-2 text-center" value="${item.quantity}" min="1" style="width: 200px;" onchange="updateQuantity('${productCode}')">
                <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity('${productCode}')">+</button>
            </div>
            <div class="col-2 text-end">
                <button type="button" class="btn btn-danger" onclick="removeItem('${productCode}')">Delete</button>
            </div>
        `;
        purchaseOrderDiv.appendChild(rowDiv);
    });

    // Dynamically update the Grand Total
    const totalDiv = document.createElement('div');
    totalDiv.classList.add('mt-4', 'text-end', 'fw-bold');
    totalDiv.innerHTML = `Grand Total: ${grandTotal}`;
    purchaseOrderDiv.appendChild(totalDiv);

    // Add a hidden input field to store the Grand Total
    const totalInput = document.createElement('input');
    totalInput.type = 'hidden';
    totalInput.name = 'grand_total';  // This is how you access it in PHP ($req->grand_total)
    totalInput.value = grandTotal;
    purchaseOrderDiv.appendChild(totalInput);

    // Add the Place Order button
    document.getElementById("theButton").innerHTML = '<button type="submit" class="btn btn-success">Place Order</button>';
}

function updateQuantity(productCode) {
    const input = document.getElementById(`quantity-${productCode}`);
    const newQuantity = parseInt(input.value);

    if (newQuantity >= 1) { // Ensure quantity is at least 1
        cart[productCode].quantity = newQuantity;
        localStorage.setItem('cart', JSON.stringify(cart));
        location.reload(); // Refresh to update totals
    } else {
        // Reset to 1 if user enters a value less than 1
        input.value = 1;
        cart[productCode].quantity = 1;
        localStorage.setItem('cart', JSON.stringify(cart));
        location.reload();
    }
}

function increaseQuantity(productCode) {
    const input = document.getElementById(`quantity-${productCode}`);
    input.value = parseInt(input.value) + 1;
    updateQuantity(productCode); // Call the new function
}

function decreaseQuantity(productCode) {
    const input = document.getElementById(`quantity-${productCode}`);
    const newValue = parseInt(input.value) - 1;
    if (newValue >= 1) {
        input.value = newValue;
        updateQuantity(productCode); // Call the new function
    }
}

// Remove Item
function removeItem(productCode) {
    delete cart[productCode];
    localStorage.setItem('cart', JSON.stringify(cart));
    if (Object.keys(cart).length === 0) {
        localStorage.removeItem('cart');
    }
    location.reload();
}