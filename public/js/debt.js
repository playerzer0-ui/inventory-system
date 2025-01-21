var select = document.getElementById("year");
var date = new Date();
var year = date.getFullYear();
for (var i = year - 4; i <= year + 3; i++) {
  var option = document.createElement('option');
  option.value = option.innerHTML = i;
  if (i === year) option.selected = true;
  select.appendChild(option);
}

function generateReport() { 
    let yearValue = document.getElementById("year").value;
    let monthValue = document.getElementById("month").value;
    let storageCodeValue = document.getElementById("storageCode").value;

    $.ajax({
        type: "get",
        url: "/getDebtReport",
        datatype: JSON,
        data: {
            storageCode: storageCodeValue,
            month: monthValue,
            year: yearValue
        },
        success: function (data) {
            // let data = JSON.parse(response);
            //console.log(response);
            populateTable(data);
            document.getElementById("excel").innerHTML = `<a href="../controller/index.php?action=excel_hutang&storageCode=${storageCodeValue}&month=${monthValue}&year=${yearValue}" target="_blank"><button class="btn btn-success">excel</button></a>`;
        }
    });
}

function populateTable(data) {
    const table = document.getElementById('reporttable');
    table.innerHTML = `
        <thead>
            <tr>
                <th>No.</th>
                <th>Invoice Date</th>
                <th>Vendor Name</th>
                <th>No_Invoice</th>
                <th>Material Name</th>
                <th>QTY</th>
                <th>price/UOM</th>
                <th>nominal</th>
                <th>total nominal</th>
                <th>tax (%)</th>
                <th>nominal after tax</th>
                <th>Payment Date</th>
                <th>Payment Amount</th>
                <th>remaining</th>
            </tr>
        </thead>
        <tbody></tbody>
    `;

    const tbody = table.querySelector('tbody');
    let rowNumber = 1;
    let totalQty = 0;
    let totalNominal = 0;
    let totalPayment = 0;
    let totalRemaining = 0;
    let tax = 0;

    data.forEach((invoice, index) => {
        const productCount = invoice.products.length;
        const paymentCount = invoice.payments.length;
        const rowCount = Math.max(productCount, paymentCount);
        let firstRow = true;

        const invoiceTotalNominal = invoice.products.reduce((sum, product) => sum + parseFloat(product.nominal), 0);
        tax = invoice.tax; // Assuming tax is a percentage value provided in the invoice data
        const nominalAfterTax = invoiceTotalNominal + (invoiceTotalNominal * (tax / 100));
        const invoiceTotalPayment = invoice.payments.reduce((sum, payment) => sum + parseFloat(payment.payment_amount), 0);
        const invoiceRemaining = nominalAfterTax - invoiceTotalPayment;

        for (let i = 0; i < rowCount; i++) {
            const tr = document.createElement('tr');

            if (firstRow) {
                tr.innerHTML += `<td rowspan="${rowCount}">${rowNumber}</td>`;
                tr.innerHTML += `<td rowspan="${rowCount}">${invoice.invoice_date}</td>`;
                tr.innerHTML += `<td rowspan="${rowCount}">${invoice.vendorName}</td>`;
                tr.innerHTML += `<td rowspan="${rowCount}">${invoice.no_invoice}</td>`;
            }

            if (i < productCount) {
                const product = invoice.products[i];
                tr.innerHTML += `<td>${product.productCode}</td>`;
                tr.innerHTML += `<td>${product.qty}</td>`;
                tr.innerHTML += `<td>${formatNumber(product.price_per_UOM)}</td>`;
                tr.innerHTML += `<td>${formatNumber(product.nominal)}</td>`;
                if (firstRow) {
                    tr.innerHTML += `<td rowspan="${rowCount}">${formatNumber(invoiceTotalNominal)}</td>`;
                    tr.innerHTML += `<td rowspan="${rowCount}">${tax}</td>`;
                    tr.innerHTML += `<td rowspan="${rowCount}">${formatNumber(nominalAfterTax)}</td>`;
                }
            } else {
                tr.innerHTML += `<td colspan="4"></td>`;
            }

            if (i < paymentCount) {
                const payment = invoice.payments[i];
                tr.innerHTML += `<td>${payment.payment_date}</td>`;
                tr.innerHTML += `<td>${formatNumber(payment.payment_amount)}</td>`;
                if (firstRow) {
                    tr.innerHTML += `<td rowspan="${rowCount}"${invoiceRemaining > 0 ? ' class="not-paid"' : ''}>${formatNumber(invoiceRemaining)}</td>`;
                }
            } else {
                tr.innerHTML += `<td colspan="2"></td>`;
            }

            tbody.appendChild(tr);
            firstRow = false;
        }

        rowNumber++;
        totalQty += invoice.products.reduce((sum, p) => sum + parseInt(p.qty), 0);
        totalNominal += invoiceTotalNominal;
        totalPayment += invoiceTotalPayment;
        totalRemaining += invoiceRemaining;
    });

    const totalRow = document.createElement('tr');
    totalRow.innerHTML = `
        <td colspan="5">Total</td>
        <td>${formatNumber(totalQty)}</td>
        <td></td>
        <td></td>
        <td>${formatNumber(totalNominal)}</td>
        <td></td>
        <td>${formatNumber(totalNominal + (totalNominal * (tax / 100)))}</td>
        <td></td>
        <td>${formatNumber(totalPayment)}</td>
        <td>${formatNumber(totalRemaining)}</td>
    `;
    tbody.appendChild(totalRow);
}

function formatNumber(number) {
    return new Intl.NumberFormat('id-ID', { style: 'decimal', maximumFractionDigits: 0 }).format(number);
}