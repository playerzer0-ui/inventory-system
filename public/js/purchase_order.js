let rowCount = document.querySelectorAll('#productTable tbody tr').length;

function applyAutocomplete(element) {
    $(element).autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '/getProductSuggestions',
                type: 'GET',
                dataType: 'json',
                data: {
                    code: request.term
                },
                success: function(data) {
                    console.log(data);
                    response(data);
                }
            });
        },
        select: function(event, ui) {
            $(element).val(ui.item.value);
            getProductDetails(element);
            return false;
        }
        //source: availableTags
    });
}

function addRow() {
    rowCount++;
    const table = document.getElementById('productTable').getElementsByTagName('tbody')[0];
    const newRow = table.insertRow();

    newRow.innerHTML = `
        <td>${rowCount}</td>
        <td><input type="text" name="kd[]" class="productCode" placeholder="Fill in" oninput="applyAutocomplete(this)" required></td>
        <td><input style="width: 300px;" type="text" name="material_display[]" placeholder="Automatic from system" readonly><input type="hidden" name="material[]"></td>
        <td><input type="number" name="qty[]" oninput="calculateNominal(this)" placeholder="Fill in" required></td>
        <td><input type="text" name="uom[]" placeholder="Fill in" required></td>
        <td><input type="number" inputmode="numeric" name="price_per_uom[]" placeholder="Automatic from system" readonly></td>
        <td><input type="text" name="nominal[]" oninput="calculateNominal(this)" placeholder="Automatic from system" readonly></td>
        <td><input type="text" name="note[]" placeholder=""></td>
        <td><button class="btn btn-danger" onclick="deleteRow(this)">Delete</button></td>
    `;
}

function getProductDetails(input) {
    const productCode = input.value;
    const row = input.parentElement.parentElement;

    $.ajax({
        url: '/getProductDetails',
        type: 'GET',
        dataType: 'json',
        data: {
            code: productCode
        },
        success: function(data) {
            console.log(data);
            if (data) {
                row.querySelector('input[name="material_display[]"]').value = data.productName;
                row.querySelector('input[name="material[]"]').value = data.productName;
                row.querySelector('input[name="price_per_uom[]"]').value = data.productPrice;
            } else {
                // Clear fields if no product is found
                row.querySelector('input[name="material_display[]"]').value = "Automatically Filled";
                row.querySelector('input[name="material[]"]').value = "";
                row.querySelector('input[name="price_per_uom[]"]').value = "Automatically Filled";
            }
        }
    });
}

function deleteRow(button) {
    const row = button.parentNode.parentNode;
    row.parentNode.removeChild(row);

    // Update row numbers
    const rows = document.getElementById('productTable').getElementsByTagName('tbody')[0].rows;
    rowCount = 0;
    for (let i = 0; i < rows.length; i++) {
        rowCount++;
        rows[i].cells[0].innerText = rowCount;
    }
}

function calculateNominal(priceInput) {
    const row = priceInput.closest('tr'); // Get the closest row to the input
    const qty = parseFloat(row.querySelector('input[name="qty[]"]').value); // Get the quantity value
    const price = parseFloat(row.querySelector('input[name="price_per_uom[]"]').value); // Get the price value

    console.log(price);
    if (!isNaN(qty) && !isNaN(price)) {
        const nominal = qty * price; // Calculate the nominal value
        row.querySelector('input[name="nominal[]"]').value = nominal.toFixed(2); // Update the nominal field
    } else {
        row.querySelector('input[name="nominal[]"]').value = ''; // Clear the nominal field if invalid input
    }
}