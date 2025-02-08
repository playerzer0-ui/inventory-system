<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Order_Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AmendController extends Controller
{
    public function amend_slip_data(Request $req)
    {
        $no_sj = $req->no_sj;
        $storageCode = $req->storageCode;
        $no_LPB = $req->no_LPB;
        $no_truk = $req->no_truk;
        $vendorCode = $req->vendorCode;
        $customerCode = $req->customerCode;
        $orderDate = $req->order_date;
        $purchase_order = $req->purchase_order;
        $status_mode = $req->status_mode;

        $productCodes = $req->input('kd');
        $qtys = $req->input('qty');
        $uoms = $req->input('uom');
        $notes = $req->input('note');
        $pageState = $req->pageState;

        DB::transaction(function () use ($no_sj, $storageCode, $no_LPB, $no_truk, $vendorCode, $customerCode, $orderDate, $purchase_order, $productCodes, $qtys, $uoms, $notes, $status_mode, $pageState) {
            $order = Order::where('nomor_surat_jalan', $no_sj)->firstOrFail();
        
            // Update order details
            $order->storageCode = $storageCode;
            $order->no_LPB = $no_LPB;
            $order->no_truk = $no_truk;
            $order->vendorCode = $vendorCode ?? "NON";
            $order->customerCode = $customerCode ?? "NON";
            $order->orderDate = $orderDate;
            $order->purchase_order = $purchase_order;
            $order->nomor_surat_jalan = $no_sj;
            $order->save();
        
            // Update, create or delete order products
            if ($productCodes) {
                foreach ($productCodes as $i => $productCode) {
                    $qty = $qtys[$i];
                    $uom = $uoms[$i];
                    $note = $notes[$i] ?? null;
            
                    // Update the existing product while retaining price_per_UOM
                    DB::update("
                        UPDATE order_products 
                        SET qty = ?, UOM = ?, note = ?
                        WHERE nomor_surat_jalan = ? AND productCode = ?
                    ", [$qty, $uom, $note, $no_sj, $productCode]);
                }
            
                // Insert new products that don’t exist
                $existingProducts = Order_Product::where('nomor_surat_jalan', $no_sj)
                                                 ->pluck('productCode')->toArray();
                $newProducts = array_diff($productCodes, $existingProducts);
            
                foreach ($newProducts as $i => $productCode) {
                    DB::insert("
                        INSERT INTO order_products (nomor_surat_jalan, repack_no_repack, moving_no_moving, 
                                                    productCode, qty, UOM, price_per_UOM, note, product_status)
                        VALUES (?, '-', '-', ?, ?, ?, 0, ?, ?)
                    ", [$no_sj, $productCode, $qtys[$i], $uoms[$i], $notes[$i] ?? null, $pageState]);
                }
            
                // Delete products that are no longer in the request
                $productsToDelete = array_diff($existingProducts, $productCodes);
                if (!empty($productsToDelete)) {
                    DB::delete("
                        DELETE FROM order_products 
                        WHERE nomor_surat_jalan = ? AND productCode IN (" . implode(',', array_fill(0, count($productsToDelete), '?')) . ")
                    ", array_merge([$no_sj], $productsToDelete));
                }
            }
        });

        return redirect()->route("dashboard");
    }
}
