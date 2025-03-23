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
  // Fetch selected values
  const storageCodeEl = document.getElementById('storageCode').value;
  const monthEl = document.getElementById('month').value;
  const yearEl = document.getElementById('year').value;

  $.ajax({
      type: "get",
      url: "/getReportStock",
      data: {
          month: monthEl,
          year: yearEl,
          storageCode: storageCodeEl

      },
      success: function (data) {
          //console.log(data);
          populateReportTable(data);
      }
  });

  // Populate the report table
}

function populateReportTable(data) {
  const userType = document.getElementById('userType').value; // Get userType from hidden input
  const tbody = document.querySelector('#reporttable tbody');
  tbody.innerHTML = ''; // Clear existing rows

  // Variables to store total sums for final row
  let totalSaldoAwalQty = 0;
  let totalinQty = 0;
  let totaloutQty = 0;
  let totalSaldoAkhirQty = 0;

  let totalSaldoAwalRupiah = 0;
  let totalinRupiah = 0;
  let totaloutRupiah = 0;
  let totalSaldoAkhirRupiah = 0;

  let item;
  let storageCode;
  let month;
  let year;

  let count = 0;
  for (let key in data) {
      if (key === "0"){
          item = data[key];
          storageCode = item.storageCode;
          month = item.month;
          year = item.year;
          continue;
      }

      count++;
      item = data[key];
      let row = document.createElement('tr');

      // No, KD, Material
      row.innerHTML = `
          <td>${count}</td>
          <td>${item.productCode}</td>
          <td>${item.productName}</td>
      `;

      // Saldo Awal
      let saldoAwalQty = item.initial_balance.totalQty;
      totalSaldoAwalQty += saldoAwalQty;

      row.innerHTML += `<td>${formatNumber(saldoAwalQty)}</td>`;

      if (userType == 1) {
          let saldoAwalRupiah = parseFloat(item.initial_balance.totalPrice);
          totalSaldoAwalRupiah += saldoAwalRupiah;

          row.innerHTML += `
              <td>${formatNumber(item.initial_balance.price_per_qty)}</td>
              <td>${formatNumber(saldoAwalRupiah)}</td>
          `;
      }

      // in
      let purchaseQty = parseInt(item.in.purchase.totalQty);
      let totalInQty = item.in.totalIn.totalQty;
      totalinQty += purchaseQty + totalInQty;

      row.innerHTML += `<td>${formatNumber(purchaseQty)}</td>`;

      if (userType == 1) {
          let purchaseRupiah = parseFloat(item.in.purchase.totalPrice);
          let totalInRupiah = parseFloat(item.in.totalIn.totalPrice);
          totalinRupiah += purchaseRupiah + totalInRupiah;

          row.innerHTML += `
              <td>${formatNumber(item.in.purchase.price_per_qty)}</td>
              <td>${formatNumber(purchaseRupiah)}</td>
              <td>${formatNumber(item.in.movingIn.totalQty)}</td>
              <td>${formatNumber(item.in.movingIn.price_per_qty)}</td>
              <td>${formatNumber(item.in.movingIn.totalPrice)}</td>
              <td>${formatNumber(item.in.repackIn.totalQty)}</td>
              <td>${formatNumber(item.in.repackIn.price_per_qty)}</td>
              <td>${formatNumber(item.in.repackIn.totalPrice)}</td>
              <td>${formatNumber(totalInQty)}</td>
              <td>${formatNumber(item.in.totalIn.price_per_qty)}</td>
              <td>${formatNumber(totalInRupiah)}</td>
          `;
      } else {
          row.innerHTML += `
              <td>${formatNumber(item.in.movingIn.totalQty)}</td>
              <td>${formatNumber(item.in.repackIn.totalQty)}</td>
              <td>${formatNumber(totalInQty)}</td>
          `;
      }

      // Barang Siap Dijual
      row.innerHTML += `<td>${formatNumber(item.ready_to_sell_items.totalQty)}</td>`;

      if (userType == 1) {
          row.innerHTML += `
              <td>${formatNumber(item.ready_to_sell_items.price_per_qty)}</td>
              <td>${formatNumber(item.ready_to_sell_items.totalPrice)}</td>
          `;
      }

      // out
      let salesQty = parseInt(item.out.sales.totalQty);
      let totalOutQty = item.out.totalOut.totalQty;
      totaloutQty += salesQty + totalOutQty;

      row.innerHTML += `<td>${formatNumber(salesQty)}</td>`;

      if (userType == 1) {
          let salesRupiah = parseFloat(item.out.sales.totalPrice);
          let totalOutRupiah = parseFloat(item.out.totalOut.totalPrice);
          totaloutRupiah += salesRupiah + totalOutRupiah;

          row.innerHTML += `
              <td>${formatNumber(item.out.sales.price_per_qty)}</td>
              <td>${formatNumber(salesRupiah)}</td>
              <td>${formatNumber(item.out.movingOut.totalQty)}</td>
              <td>${formatNumber(item.out.movingOut.price_per_qty)}</td>
              <td>${formatNumber(item.out.movingOut.totalPrice)}</td>
              <td>${formatNumber(item.out.repackOut.totalQty)}</td>
              <td>${formatNumber(item.out.repackOut.price_per_qty)}</td>
              <td>${formatNumber(item.out.repackOut.totalPrice)}</td>
              <td>${formatNumber(totalOutQty)}</td>
              <td>${formatNumber(item.out.totalOut.price_per_qty)}</td>
              <td>${formatNumber(totalOutRupiah)}</td>
          `;
      } else {
          row.innerHTML += `
              <td>${formatNumber(item.out.movingOut.totalQty)}</td>
              <td>${formatNumber(item.out.repackOut.totalQty)}</td>
              <td>${formatNumber(totalOutQty)}</td>
          `;
      }

      // Saldo Akhir
      let saldoAkhirQty = item.final_balance.totalQty;
      totalSaldoAkhirQty += saldoAkhirQty;

      row.innerHTML += `<td>${formatNumber(saldoAkhirQty)}</td>`;

      if (userType == 1) {
          let saldoAkhirRupiah = parseFloat(item.final_balance.totalPrice);
          totalSaldoAkhirRupiah += saldoAkhirRupiah;

          row.innerHTML += `
              <td>${formatNumber(item.final_balance.price_per_qty)}</td>
              <td>${formatNumber(saldoAkhirRupiah)}</td>
          `;
      }

      tbody.appendChild(row);

  }
  // Optionally, you can add a row for totals if needed, based on userType
  document.getElementById("excel").innerHTML = `<a href="/excel_stock?storageCode=${storageCode}&month=${month}&year=${year}" target="_blank"><button class="btn btn-success">excel</button></a>`;

}

function formatNumber(number) {
  return new Intl.NumberFormat('id-ID', { style: 'decimal', maximumFractionDigits: 0 }).format(number);
}