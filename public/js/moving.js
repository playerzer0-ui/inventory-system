let pageState = document.getElementById("pageState").value;

window.onload = function() {
    getMovingNO();
    if (pageState === "amend_moving") {
        updateCOGSAndNominals();
    }
};

// document.addEventListener('DOMContentLoaded', function() {
//     const form = document.getElementById('myForm');

//     form.addEventListener('keydown', function(event) {
//         if (event.key === 'Enter') {
//             event.preventDefault();
//             return false;
//         }
//     });
// });

// document.getElementById('storageCodeSender').addEventListener('change', updateAllHPP);
// document.getElementById('moving_date').addEventListener('change', updateAllHPP);


if (!pageState.includes("amend")){
    document.addEventListener("DOMContentLoaded", function() {
        let invoice_dateEl = document.getElementById("moving_date");
    
        // Get today's date
        let today = new Date();
    
        // Format the date to YYYY-MM-DD
        let year = today.getFullYear();
        let month = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based, so add 1 and pad with zero if needed
        let day = String(today.getDate()).padStart(2, '0'); // Pad day with zero if needed
    
        let formattedDate = `${year}-${month}-${day}`;
    
        // Set the value of the date input to today's date
        invoice_dateEl.value = formattedDate;
    });
}

function addRow(tableId) {
    var table = document.getElementById(tableId);
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);

    row.innerHTML = `<td>${rowCount}</td>
        <td><input name="kd[]" class="productCode" oninput="applyAutocomplete(this)" type="text" placeholder="di isi" required/></td>
        <td><input name="productName[]" type="text" placeholder="Otomatis" readonly/></td>
        <td><input name="qty[]" type="text" placeholder="di isi" oninput="calculateNominal(this)" required/></td>
        <td><input name="uom[]" type="text" placeholder="di isi" required/></td>
        <td><input name="price_per_uom[]" type="text" placeholder="otomatis" readonly/></td>
        <td><input type="number" inputmode="numeric" name="nominal[]" placeholder="Otomatis" readonly></td>
        <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Remove</button></td>`;

    applyAutocomplete(row.querySelector('.productCode'));
}

function removeRow(button) {
    var row = button.parentNode.parentNode;
    var tableBody = row.parentNode;
    tableBody.removeChild(row);
    reNumberRows(tableBody);
}

function reNumberRows(tableBody) {
    var rows = tableBody.rows;
    for (var i = 0; i < rows.length; i++) {
        rows[i].cells[0].innerText = i + 1;
    }
}

function applyAutocomplete(input) {
    $(input).autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '/getProductSuggestions',
                type: 'GET',
                dataType: 'json',
                data: {
                    code: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        select: function(event, ui) {
            $(this).val(ui.item.value);
            getProductDetails(this);
            return false;
        }
    });
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
                row.querySelector('input[name="productName[]"]').value = data.productName;
                getHPP(input);
            } else {
                row.querySelector('input[name="productName[]"]').value = "Automatic from the system";
            }
        }
    });
}

function updateCOGSAndNominals() {
    const rows = document.querySelectorAll("#materialTable tbody tr");
    
    rows.forEach(row => {
        const productCodeInput = row.querySelector('.productCode');
        const productCode = productCodeInput.value;

        if (productCode) {
            getHPP(productCodeInput, updateNominal);
        }
    });
}

function getHPP(input){
    const productCode = input.value;
    const row = input.parentElement.parentElement;
    const storageCode = document.getElementById("storageCodeSender").value;
    let order_date = document.getElementById("moving_date").value;
    let date = new Date(order_date);

    let month = date.getMonth() + 1;
    let year = date.getFullYear();

    $.ajax({
        type: "get",
        url: "/getHPP",
        dataType: 'json',
        data: {
            productCode: productCode,
            storageCode: storageCode,
            month: month,
            year: year
        },
        success: function (data) {
            row.querySelector('input[name="price_per_uom[]"]').value = data;
            calculateNominal(row.querySelector('input[name="qty[]"]'));
        }
    });
}

function updateNominal(row) {
    const qty = parseFloat(row.querySelector('input[name="qty[]"]').value); // Get the quantity value
    const price = parseFloat(row.querySelector('input[name="price_per_uom[]"]').value); // Get the updated COGS value

    if (!isNaN(qty) && !isNaN(price)) {
        const nominal = qty * price; // Calculate the nominal value
        row.querySelector('input[name="nominal[]"]').value = nominal.toFixed(2); // Update the nominal field
    } else {
        row.querySelector('input[name="nominal[]"]').value = ''; // Clear the nominal field if invalid input
    }
}

function updateAllHPP() {
    let rows = document.querySelectorAll('#materialTable tbody tr');
    
    rows.forEach(row => {
        let productCodeInput = row.querySelector('input[name="kd[]"]');
        if (productCodeInput && productCodeInput.value) {
            getHPP(productCodeInput);
        }
    });
}


function calculateNominal(priceInput) {
    const row = priceInput.closest('tr'); // Get the closest row to the input
    const qty = parseFloat(row.querySelector('input[name="price_per_uom[]"]').value); // Get the quantity value
    const price = parseFloat(priceInput.value); // Get the price value

    if (!isNaN(qty) && !isNaN(price)) {
        const nominal = qty * price; // Calculate the nominal value
        row.querySelector('input[name="nominal[]"]').value = nominal.toFixed(2); // Update the nominal field
    } else {
        row.querySelector('input[name="nominal[]"]').value = ''; // Clear the nominal field if invalid input
    }
}

function getMovingNO() {
    let storageCodeEl = document.getElementById('storageCodeSender').value;
    let noMovingEl = document.getElementById('no_moving');
    let order_date = document.getElementById("moving_date").value;
    let date = new Date(order_date);

    let month = date.getMonth() + 1;
    let year = date.getFullYear();

    $.ajax({
        type: "get",
        url: "generate_LPB_SJK_INV",
        data: {
            state: "SJP",
            storageCode: storageCodeEl,
            month: month,
            year: year
        },
        success: function(response) {
            noMovingEl.value = response;
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
        }
    });
}