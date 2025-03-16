
// Decrease Quantity
function decreaseQuantity(productCode) {
    const input = document.getElementById(`quantity-${productCode}`);
    let value = parseInt(input.value);
    if (value > 1) {
        input.value = value - 1;
    }
    updateGrandTotal();
}

// Increase Quantity
function increaseQuantity(productCode) {
    const input = document.getElementById(`quantity-${productCode}`);
    input.value = parseInt(input.value) + 1;
    updateGrandTotal();
}

// Remove Item
function removeItem(productCode) {
    // Find the closest row based on the button clicked
    let button = event.target;
    let row = button.closest(".row");

    if (row) {
        row.remove();
        updateGrandTotal(); // Update the grand total after removal
    }
}

// Function to recalculate and update the Grand Total
function updateGrandTotal() {
    let total = 0;

    document.querySelectorAll(".row").forEach((row) => {
        let priceInput = row.querySelector("input[name='price_per_uom[]']");
        let qtyInput = row.querySelector("input[name='qty[]']");

        if (priceInput && qtyInput) {
            let price = parseFloat(priceInput.value);
            let quantity = parseInt(qtyInput.value);

            if (!isNaN(price) && !isNaN(quantity)) {
                total += price * quantity;
            }
        }
    });

    document.getElementById("grandTotal").innerText = `Grand Total: ${total.toFixed(2)}`;
    document.getElementById("grand_total").value = total.toFixed(2);
}
