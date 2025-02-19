PAY-wq3rsd/2025-11-11

SELECT o.nomor_surat_jalan, op.productCode, o.orderDate, op.qty, op.product_status 
FROM order_products op JOIN orders o ON (o.nomor_surat_jalan = op.nomor_surat_jalan) 
WHERE op.product_status = "out" 
GROUP BY (o.orderDate) 
ORDER BY o.orderDate;

task:
[X] delete slips, invoice, payment, moving, repack
make moving average
customer interface
pdf excel
cloud