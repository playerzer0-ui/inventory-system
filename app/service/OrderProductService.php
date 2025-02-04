<?php 

namespace App\Services;

use Illuminate\Support\Facades\DB;

class OrderProductService
{
    public function getOrderProducts($no_sj, $status)
    {
        if ($status == "in" || $status == "out") {
            return DB::table('order_products as op')
                ->join('products as p', 'op.productCode', '=', 'p.productCode')
                ->where('op.nomor_surat_jalan', $no_sj)
                ->select(
                    'op.nomor_surat_jalan',
                    'op.productCode',
                    'p.productName',
                    'op.qty',
                    'op.uom',
                    'op.price_per_UOM',
                    'op.note',
                    'op.product_status'
                )
                ->get();
        } elseif ($status == "repack") {
            return DB::table('order_products as op')
                ->join('products as p', 'op.productCode', '=', 'p.productCode')
                ->where('op.repack_no_repack', $no_sj)
                ->select(
                    'op.repack_no_repack',
                    'op.productCode',
                    'p.productName',
                    'op.qty',
                    'op.uom',
                    'op.price_per_UOM',
                    'op.note',
                    'op.product_status'
                )
                ->get();
        } else {
            return DB::table('order_products as op')
                ->join('products as p', 'op.productCode', '=', 'p.productCode')
                ->where('op.moving_no_moving', $no_sj)
                ->select(
                    'op.moving_no_moving',
                    'op.productCode',
                    'p.productName',
                    'op.qty',
                    'op.uom',
                    'op.price_per_UOM',
                    'op.note',
                    'op.product_status'
                )
                ->get();
        }
    }

    public function getOrderByNoSJ($no_sj)
    {
        $result = DB::table('orders as o')
        ->join('customers as c', 'o.customerCode', '=', 'c.customerCode')
        ->where('o.nomor_surat_jalan', $no_sj)
        ->select(
            'o.nomor_surat_jalan',
            'o.storageCode',
            'o.no_LPB',
            'o.no_truk',
            'o.vendorCode',
            'o.customerCode',
            'c.customerName',
            'c.customerAddress',
            'c.customerNPWP',
            'o.orderDate',
            'o.purchase_order',
            'o.status_mode'
        )
        ->first();

        return $result;
    }
}

?>