let rowCount = document.querySelectorAll('#productTable tbody tr').length;
let pageState = document.getElementById("pageState").value;

if(pageState.includes("slip_in")){
    var NO_LPB = document.getElementById("no_LPB").value.split("/");
}
var NO_SJ = document.getElementById("no_sj").value.split("/");

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('myForm');

    form.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            return false;
        }
    });
});

if (!pageState.includes("amend")){
    document.addEventListener("DOMContentLoaded", function() {
        let invoice_dateEl = document.getElementById("order_date");
    
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

function applyAutocomplete(element) {
    $(element).autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '/getProductSuggestions',
                type: 'GET',
                dataType: 'json',
                data: {
                    action: 'getProductSuggestions',
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
        <td><input type="text" name="kd[]" placeholder="fill in" class="productCode" oninput="applyAutocomplete(this)" required></td>
        <td><input style="width: 300px;" type="text" name="material_display[]" value="Automatically Filled" readonly><input type="hidden" name="material[]"></td>
        <td><input type="number" name="qty[]" placeholder="fill in" required></td>
        <td><input type="text" name="uom[]" value="" placeholder="fill in" required></td>
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
            action: 'getProductDetails',
            code: productCode
        },
        success: function(data) {
            if (data) {
                row.querySelector('input[name="material_display[]"]').value = data.productName;
                row.querySelector('input[name="material[]"]').value = data.productName;
            } else {
                // Clear fields if no product is found
                row.querySelector('input[name="material_display[]"]').value = "Automatically Filled";
                row.querySelector('input[name="material[]"]').value = "";
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

function getLPB(){
    let storageCodeEl = document.getElementById('storageCode').value;
    let noLPBEl = document.getElementById('no_lpb_display');
    let noLPBHiddenEl = document.getElementById('no_LPB');
    let order_date = document.getElementById("order_date").value;
    let date = new Date(order_date);

    let month = date.getMonth() + 1;
    let year = date.getFullYear();

    $.ajax({
        type: "get",
        url: "/generate_LPB",
        data: {
            action: "generate_LPB",
            storageCode: storageCodeEl,
            month: month,
            year: year
        },
        success: function (response) {
            let arr = response.split("/");
            if(pageState == "amend_slip_in" && NO_LPB[2] === arr[2] && parseInt(NO_LPB[3]) === parseInt(arr[3]) && parseInt(NO_LPB[4]) === parseInt(arr[4])){
                noLPBEl.value = NO_LPB[0] + "/" + NO_LPB[1] + "/" + NO_LPB[2] + "/" + NO_LPB[3] + "/" + NO_LPB[4];
                noLPBHiddenEl.value = NO_LPB[0] + "/" + NO_LPB[1] + "/" + NO_LPB[2] + "/" + NO_LPB[3] + "/" + NO_LPB[4];
            }
            else{
                noLPBEl.value = response;
                noLPBHiddenEl.value = response;
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
        }
    });
}

function getSJ(){
    let storageCodeEl = document.getElementById('storageCode').value;
    let no_sjEl = document.getElementById('no_sj');
    let order_date = document.getElementById("order_date").value;
    let date = new Date(order_date);

    let month = date.getMonth() + 1;
    let year = date.getFullYear();

    $.ajax({
        type: "get",
        url: "/generate_SJ",
        data: {
            action: "generate_SJ",
            storageCode: storageCodeEl,
            month: month,
            year: year
        },
        success: function (response) {
            let arr = response.split("/");
            if(pageState == "amend_slip_out" && NO_SJ[2] === arr[2] && parseInt(NO_SJ[3]) === parseInt(arr[3]) && parseInt(NO_SJ[4]) === parseInt(arr[4])){
                no_sjEl.value = NO_SJ[0] + "/" + NO_SJ[1] + "/" + NO_SJ[2] + "/" + NO_SJ[3] + "/" + NO_SJ[4];
            }
            else{
                no_sjEl.value = response;
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
        }
    });
}

