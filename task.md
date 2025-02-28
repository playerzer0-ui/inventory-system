PAY-wq3rsd/2025-11-11

SELECT o.nomor_surat_jalan, op.productCode, o.orderDate, op.qty, op.product_status 
FROM order_products op JOIN orders o ON (o.nomor_surat_jalan = op.nomor_surat_jalan) 
WHERE op.product_status = "out" AND op.productCode = 'P3766'
GROUP BY (o.orderDate) 
ORDER BY o.orderDate;

PO-f35791ce

CREATE TABLE trucks(
    truckID,
    truckSize,
    truckMode
);

task:
[X] delete slips, invoice, payment, moving, repack
[X] make moving average
[X] customer interface
[X] pdf excel
purchase order interface with UI simple
truck algorithm automatically assign truck
truck UI, delivery interface, tick boxes to trucks and orders assigned by them

cloud