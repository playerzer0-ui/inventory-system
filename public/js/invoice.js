var pageState = document.getElementById("pageState").value;

document.addEventListener("DOMContentLoaded", function() {
    if (pageState === "amend_invoice_moving") {
        updateCOGSAndNominals();
    }
});


// if (!pageState.includes("amend")){
//     document.addEventListener("DOMContentLoaded", function() {
//         let invoice_dateEl = document.getElementById("invoice_date");
    
//         // Get today's date
//         let today = new Date();
    
//         // Format the date to YYYY-MM-DD
//         let year = today.getFullYear();
//         let month = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based, so add 1 and pad with zero if needed
//         let day = String(today.getDate()).padStart(2, '0'); // Pad day with zero if needed
    
//         let formattedDate = `${year}-${month}-${day}`;
    
//         // Set the value of the date input to today's date
//         invoice_dateEl.value = formattedDate;
//     });
// }

function updateCOGSAndNominals() {
    const rows = document.querySelectorAll("#productTable tbody tr");
    
    rows.forEach(row => {
        const productCodeInput = row.querySelector('.productCode');
        const productCode = productCodeInput.value;

        if (productCode) {
            getHPP(productCodeInput, updateNominal);
        }
    });
}

function getHPP(input, callback){
    const productCode = input.value;
    const row = input.closest('tr');
    const storageCode = pageState.includes("moving") ? document.getElementById("storageCodeSender").value : document.getElementById("storageCode").value;
    let order_date = document.getElementById("invoice_date").value;
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
            console.log(data);
            row.querySelector('input[name="price_per_uom[]"]').placeholder = data.toFixed(0);
            callback(row);
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

    calculateTotalNominal(); // Update total nominal after each row update
}

function getMovingDetailsFromMovingNo(){
    let storageCodeSender = document.getElementById("storageCodeSender");
    let storageCodeReceiver = document.getElementById("storageCodeReceiver");
    let no_moving = document.getElementById("no_moving").value;
    let moving_date = document.getElementById("moving_date");

    // First AJAX call to get moving details
    $.ajax({
        type: "get",
        url: "/getMovingDetails",
        data: {
            no_moving: no_moving
        }
    }).done(function(data) {
        //const data = JSON.parse(response);
        storageCodeSender.value = data.storageCodeSender;
        storageCodeReceiver.value = data.storageCodeReceiver;
        moving_date.value = data.moving_date;

        generateNoInvoice();

        return getOrderProducts(no_moving, "moving")});
}


function getDetailsFromSJ(){
    let no_sjEl = document.getElementById("no_sj").value;
    let storageCodeEl = document.getElementById("storageCode");
    let pageState = document.getElementById("pageState").value;
    let no_LPBEl;
    let no_trukEl;
    let vendorCodeEl;
    let purchaseOrderEl;
    let customerCode;
    let customerAddress;
    let npwp;

    if(pageState == "in"){
        no_LPBEl = document.getElementById("no_LPB");
        no_trukEl = document.getElementById("no_truk");
        vendorCodeEl = document.getElementById("vendorCode");
        purchaseOrderEl = document.getElementById("purchase_order");
    }
    else{
        customerCode = document.getElementById("customerCode");
        customerAddress = document.getElementById("customerAddress");
        npwp = document.getElementById("npwp");
    }

    $.ajax({
        type: "get",
        url: "/getOrderByNoSJ",
        data: {
            no_sj: no_sjEl
        },
        success: function (data) {
            const table = document.getElementById('productTable').getElementsByTagName('tbody')[0];
            console.log(data);
            //const data = JSON.parse(response);
            
            if(pageState == "in"){
                storageCodeEl.value = data.storageCode;
                no_LPBEl.value = data.no_LPB;
                no_trukEl.value = data.no_truk;
                vendorCodeEl.value = data.vendorCode;
                purchaseOrderEl.value = data.purchase_order;
            }
            else{
                storageCodeEl.value = data.storageCode;
                customerCode.value = data.customerCode;
                customerAddress.value = data.customerAddress;
                npwp.value = data.customerNPWP;
                generateNoInvoice();
            }
        }
    });

    getOrderProducts(no_sjEl, "in");
}

function getOrderProducts(no_id, status){
    $.ajax({
        type: "get",
        url: "/getOrderProducts",
        data: {
            status: status,
            no_sj: no_id
        },
        success: function (data) {
            const table = document.getElementById('productTable').getElementsByTagName('tbody')[0];
            console.log(data);
            let newRow;
            let rowCount = 1;
            table.innerHTML = "";

            data.forEach(item => {
                newRow = table.insertRow();
                newRow.innerHTML = `
                    <td>${rowCount}</td>
                    <td><input type="text" name="kd[]" value="${item.productCode}" class="productCode" readonly></td>
                    <td><input style="width: 300px;" value="${item.productName}" type="text" name="material_display[]" readonly><input type="hidden" value="${item.productName}" name="material[]"></td>
                    <td><input type="number" value="${item.qty}" name="qty[]" readonly></td>
                    <td><input type="text" value="${item.uom}" name="uom[]" readonly></td>
                    <td><input type="number" inputmode="numeric" name="price_per_uom[]" placeholder="Automatic from the system" oninput="calculateNominal(this)" required></td>
                    <td><input type="text" name="nominal[]" placeholder="otomatis dari sistem" readonly></td>
                `;
            });

            if(pageState.includes("moving")){
                updateCOGSAndNominals();
            }
        }
    });
}

function generateNoInvoice(){
    let invoice_dateEl = document.getElementById("invoice_date");
    let no_invoiceEl = document.getElementById("no_invoice");
    let sCode;
    let old_invoiceEl;
    if(pageState.includes("moving")){
        sCode = document.getElementById("storageCodeSender").value;
    }
    else{
        sCode = document.getElementById("storageCode").value;
    }

    if(pageState == "amend_invoice_moving"){
        old_invoiceEl = document.getElementById("old_invoice").value;
    }

    let dateValue = invoice_dateEl.value;
    let date = new Date(dateValue);
    let month = date.getMonth() + 1;
    let year = date.getFullYear();

    $.ajax({
        url: '/generate_LPB_SJK_INV',
        type: 'GET',
        data: {
            state: "INV",
            storageCode: sCode,
            month: month,
            year: year
        },
        success: function (response) {
            no_invoiceEl.value = response;
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
        }
    });
}

function calculateNominal(priceInput) {
    const row = priceInput.closest('tr'); // Get the closest row to the input
    const qty = parseFloat(row.querySelector('input[name="qty[]"]').value); // Get the quantity value
    const price = parseFloat(priceInput.value); // Get the price value

    if (!isNaN(qty) && !isNaN(price)) {
        const nominal = qty * price; // Calculate the nominal value
        row.querySelector('input[name="nominal[]"]').value = nominal.toFixed(2); // Update the nominal field
    } else {
        row.querySelector('input[name="nominal[]"]').value = ''; // Clear the nominal field if invalid input
    }

    calculateTotalNominal();
}

function calculateTotalNominal() {
    const nominalInputs = document.querySelectorAll('input[name="nominal[]"]'); // Get all nominal inputs
    let total = 0;

    nominalInputs.forEach(input => {
        const nominal = parseFloat(input.value);
        if (!isNaN(nominal)) {
            total += nominal; // Sum up the nominal values
        }
    });

    document.getElementById('totalNominal').value = total.toFixed(2); 
    calculatePPN();
    calculatePayAmount();
}

function calculatePPN(){
    let nominal = document.getElementById('totalNominal').value;
    let taxPPN = document.getElementById('taxPPN');
    let tax = document.getElementById('tax').value;

    taxPPN.value = nominal * (tax / 100);
}

function calculatePayAmount(){
    let nominal = document.getElementById('totalNominal').value;
    let taxPPN = document.getElementById('taxPPN').value;
    let amount_paid = document.getElementById('amount_paid');

    amount_paid.value = parseFloat(nominal) + parseFloat(taxPPN);
}