function addRow(tableId) {
    var table = document.getElementById(tableId);
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);

    if (tableId === "materialstartTable") {
        row.innerHTML = `<td>${rowCount}</td>
        <td><input name="kd_start[]" class="productCode" oninput="applyAutocomplete(this)" type="text" placeholder="fill in" required/></td>
        <td><input name="material_start[]" type="text" placeholder="Automatic From System" readonly/></td>
        <td><input name="qty_start[]" type="text" placeholder="fill in" required/></td>
        <td><input name="uom_start[]" type="text" placeholder="fill in" required/></td>
        <td><input name="note_start[]" type="text" /></td>
        <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Remove</button></td>`;
    } else {
        row.innerHTML = `<td>${rowCount}</td>
        <td><input name="kd_end[]" class="productCode" oninput="applyAutocomplete(this)" type="text" placeholder="fill in" required/></td>
        <td><input name="material_end[]" type="text" placeholder="Automatic From System" readonly/></td>
        <td><input name="qty_end[]" type="text" placeholder="fill in" required/></td>
        <td><input name="uom_end[]" type="text" placeholder="fill in" required/></td>
        <td><input name="note_end[]" type="text" /></td>
        <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Remove</button></td>`;
    }

    applyAutocomplete(row.querySelector('.productCode'));
}

function removeRow(button) {
    // Get the row to be removed
    var row = button.parentNode.parentNode;
    // Get the table body
    var tableBody = row.parentNode;
    // Remove the row
    tableBody.removeChild(row);

    // Re-number the rows
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
                    action: 'getProductSuggestions',
                    term: request.term
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
    const tableId = row.parentElement.parentElement.id;

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
                if (tableId === "materialstartTable") {
                    row.querySelector('input[name="material_start[]"]').value = data.productName;
                } else if (tableId === "materialBaruTable") {
                    row.querySelector('input[name="material_end[]"]').value = data.productName;
                }
            } else {
                if (tableId === "materialstartTable") {
                    row.querySelector('input[name="material_start[]"]').value = "Automatic From System";
                } else if (tableId === "materialBaruTable") {
                    row.querySelector('input[name="material_end[]"]').value = "Automatic From System";
                }
            }
        }
    });
}

function getRepackNO() {
    let storageCodeEl = document.getElementById('storageCode').value;
    let noRepackEl = document.getElementById('no_repack');
    let order_date = document.getElementById("repack_date").value;
    let date = new Date(order_date);

    let month = date.getMonth() + 1;
    let year = date.getFullYear();

    $.ajax({
        type: "get",
        url: "/generate_LPB_SJK_INV",
        data: {
            state: "SJR",
            storageCode: storageCodeEl,
            month: month,
            year: year
        },
        success: function(response) {
            noRepackEl.value = response;
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
        }
    });
}
