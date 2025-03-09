PAY-wq3rsd/2025-11-11

SELECT o.nomor_surat_jalan, op.productCode, o.orderDate, op.qty, op.product_status 
FROM order_products op JOIN orders o ON (o.nomor_surat_jalan = op.nomor_surat_jalan) 
WHERE op.product_status = "out" AND op.productCode = 'P3766'
GROUP BY (o.orderDate) 
ORDER BY o.orderDate;

PO-f35791ce

0 - 5000 (S)
5001 - 15000 (M)
15001+ (L)

task:
[X] delete slips, invoice, payment, moving, repack
[X] make moving average
[X] customer interface
[X] pdf excel
[X] purchase order interface with UI simple
[X] truck algorithm automatically assign truck
[X] truck UI, delivery interface, tick boxes to trucks and orders assigned by them
[X] purchase order beautify
[] cloud

get all orders with the truck labeled out
count the number of times each truck has done it
check the quantity

(
    SELECT 
        p.productCode,
        p.productName,
        o.storageCode,
        MONTH(i.invoice_date) AS saldoMonth,
        YEAR(i.invoice_date) AS saldoYear,
        SUM(op.qty) AS totalQty,
        ROUND(SUM(op.price_per_UOM * op.qty) / SUM(op.qty)) AS avgPrice,
        op.product_status
    FROM products AS p
    JOIN order_products AS op ON p.productCode = op.productCode
    JOIN orders AS o ON op.nomor_surat_jalan = o.nomor_surat_jalan
    JOIN invoices AS i ON o.nomor_surat_jalan = i.nomor_surat_jalan
    WHERE MONTH(i.invoice_date) = 8
    AND YEAR(i.invoice_date) = 2024
    AND op.product_status IN ('in', 'out', 'moving')
    AND (o.storageCode = 'APA')
    GROUP BY 
        p.productCode,
        p.productName,
        o.storageCode,
        saldoMonth,
        saldoYear,
        op.product_status
)
UNION ALL
(
    SELECT 
        p.productCode,
        p.productName,
        r.storageCode,
        MONTH(r.repack_date) AS saldoMonth,
        YEAR(r.repack_date) AS saldoYear,
        SUM(op.qty) AS totalQty,
        ROUND(SUM(op.price_per_UOM * op.qty) / SUM(op.qty)) AS avgPrice,
        op.product_status
    FROM products AS p
    JOIN order_products AS op ON p.productCode = op.productCode
    JOIN repacks AS r ON op.repack_no_repack = r.no_repack
    WHERE MONTH(r.repack_date) = 8
    AND YEAR(r.repack_date) = 2024
    AND op.product_status IN ('repack_start', 'repack_end')
    AND (r.storageCode = 'APA')
    GROUP BY 
        p.productCode,
        p.productName,
        r.storageCode,
        saldoMonth,
        saldoYear,
        op.product_status
)
ORDER BY 
    CASE 
        WHEN product_status = 'in' THEN 1
        WHEN product_status = 'moving' THEN 2
        WHEN product_status = 'repack_start' THEN 3
        WHEN product_status = 'repack_end' THEN 4
        WHEN product_status = 'out' THEN 5
        ELSE 6
    END;
