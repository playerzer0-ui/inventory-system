<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Moving;
use App\Models\Order;
use App\Models\Order_Product;
use App\Models\Payment;
use App\Models\Purchase_Order;
use App\Models\Repack;
use App\Service\OrderProductService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AmendController extends Controller
{
    protected $orderProductService;

    public function __construct(OrderProductService $orderProductService)
    {
        $this->orderProductService = $orderProductService;
    }

    public function amend_slip_data(Request $req)
    {
        $no_sj = $req->no_sj;
        $old_sj = $req->old_sj;
        $storageCode = $req->storageCode;
        $no_LPB = $req->no_LPB;
        $no_truk = $req->no_truk;
        $vendorCode = $req->vendorCode;
        $customerCode = $req->customerCode;
        $orderDate = $req->order_date;
        $purchase_order = $req->purchase_order;

        $productCodes = $req->input('kd');
        $qtys = $req->input('qty');
        $uoms = $req->input('uom');
        $notes = $req->input('note');
        $pageState = $req->pageState;

        if($pageState != "in"){
            $this->orderProductService->update_SJ($old_sj, $no_sj);
        }

        try {
            DB::beginTransaction();
    
            $order = Order::where('nomor_surat_jalan', $no_sj)->firstOrFail();
    
            // Update order details
            $order->update([
                'storageCode' => $storageCode,
                'no_LPB' => $no_LPB,
                'no_truk' => $no_truk,
                'vendorCode' => $vendorCode,
                'customerCode' => $customerCode,
                'orderDate' => $orderDate,
                'purchase_order' => $purchase_order,
                'nomor_surat_jalan' => $no_sj,
            ]);
    
            // Process products
            if ($productCodes) {
                foreach ($productCodes as $i => $productCode) {
                    $qty = $qtys[$i];
                    $uom = $uoms[$i];
                    $note = $notes[$i] ?? null;
    
                    // Update existing product while retaining price_per_UOM
                    Order_Product::where('nomor_surat_jalan', $no_sj)
                        ->where('productCode', $productCode)
                        ->update([
                            'qty' => $qty,
                            'UOM' => $uom,
                            'note' => $note,
                        ]);
                }
    
                // Insert new products that don’t exist
                $existingProducts = Order_Product::where('nomor_surat_jalan', $no_sj)
                    ->pluck('productCode')
                    ->toArray();
    
                $newProducts = array_diff($productCodes, $existingProducts);
                foreach ($newProducts as $i => $productCode) {
                    Order_Product::create([
                        'nomor_surat_jalan' => $no_sj,
                        'repack_no_repack' => '-',
                        'moving_no_moving' => '-',
                        "PO_no_PO" => "-",
                        'productCode' => $productCode,
                        'qty' => $qtys[$i],
                        'UOM' => $uoms[$i],
                        'price_per_UOM' => 0,
                        'note' => $notes[$i] ?? null,
                        'product_status' => $pageState,
                    ]);
                }
    
                // Delete products that are no longer in the request
                $productsToDelete = array_diff($existingProducts, $productCodes);
                if (!empty($productsToDelete)) {
                    Order_Product::where('nomor_surat_jalan', $no_sj)
                        ->whereIn('productCode', $productsToDelete)
                        ->delete();
                }
            }
            else{
                Order_Product::where("nomor_surat_jalan", $no_sj)->delete();
            }
    
            DB::commit();
            session()->flash('msg', 'updated successfully');
            return redirect()->route("dashboard");
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('msg', 'ERROR: ' . $e->getMessage());
            return redirect()->route("dashboard");
        }

        return redirect()->route("dashboard");
    }

    public function amend_invoice_data(Request $req)
    {
        $no_sj = $req->no_sj;
        $no_moving = $req->no_moving;
        $invoice_date = $req->invoice_date;
        $no_invoice = $req->no_invoice;
        $no_faktur = $req->no_faktur;
        $tax = $req->tax;

        $productCodes = $req->input('kd');
        $price_per_uom = $req->input("price_per_uom");
        $pageState = $req->pageState;

        try {
            DB::beginTransaction();
    
            $invoice = ($pageState === 'moving')
                ? Invoice::where('no_moving', $no_moving)->firstOrFail()
                : Invoice::where('nomor_surat_jalan', $no_sj)->firstOrFail();
            
            // Update invoice details
            $invoice->update([
                'invoice_date' => $invoice_date,
                'no_invoice' => $no_invoice,
                'no_faktur' => $no_faktur,
                'tax' => $tax,
            ]);
    
            // Update product prices
            for($i = 0; $i < count($productCodes); $i++){
                DB::statement("
                    UPDATE order_products 
                    SET price_per_UOM = ?
                    WHERE productCode = ? 
                    AND (nomor_surat_jalan = ? OR moving_no_moving = ?)
                ", [$price_per_uom[$i], $productCodes[$i], $no_sj, $no_moving]);
            }
    
            DB::commit();
            session()->flash('msg', 'updated successfully');
            return redirect()->route("dashboard");
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('msg', 'ERROR: ' . $e->getMessage());
            return redirect()->route("dashboard");
        }
    }

    public function amend_payment_data(Request $req)
    {
        $payment_date = $req->payment_date;
        $payment_amount = $req->payment_amount;
        $payment_id = $req->payment_id;

        try{
            DB::beginTransaction();

            $payment = Payment::where("payment_id", $payment_id);
            $payment->update([
                "payment_date" => $payment_date,
                "payment_amount" => $payment_amount
            ]);

            DB::commit();
            session()->flash('msg', 'updated successfully');
            return redirect()->route("dashboard");
        }
        catch (Exception $e) {
            DB::rollBack();
            session()->flash('msg', 'ERROR: ' . $e->getMessage());
            return redirect()->route("dashboard");
        }
    }

    public function amend_repack_data(Request $req)
    {
        $storageCode = $req->storageCode;
        $no_repack = $req->no_repack;
        $old_repack = $req->old_repack;
        $repack_date = $req->repack_date;

        $kd_start = $req->input("kd_start", []);
        $qty_start = $req->input("qty_start", []);
        $uom_start = $req->input("uom_start", []);
        $note_start = $req->input("note_start", []);

        $kd_end = $req->input("kd_end", []);
        $qty_end = $req->input("qty_end", []);
        $uom_end = $req->input("uom_end", []);
        $note_end = $req->input("note_end", []);

        
        try {
            DB::beginTransaction();

            // Delete old order products
            Order_Product::where('repack_no_repack', $old_repack)->delete();

            // Update the repack
            Repack::where('no_repack', $old_repack)->update([
                'no_repack' => $no_repack,
                'repack_date' => $repack_date,
                'storageCode' => $storageCode,
            ]);

            // Add new order products for 'repack_start'
            foreach ($kd_start as $i => $kd) {
                Order_Product::create([
                    'nomor_surat_jalan' => '-',
                    'repack_no_repack' => $no_repack,
                    'moving_no_moving' => '-',
                    "PO_no_PO" => "-",
                    'productCode' => $kd,
                    'qty' => $qty_start[$i],
                    'UOM' => $uom_start[$i],
                    'price_per_UOM' => 0,
                    'note' => $note_start[$i] ?? null,
                    'product_status' => 'repack_start',
                ]);
            }

            // Add new order products for 'repack_end'
            foreach ($kd_end as $i => $kd) {
                Order_Product::create([
                    'nomor_surat_jalan' => '-',
                    'repack_no_repack' => $no_repack,
                    'moving_no_moving' => '-',
                    "PO_no_PO" => "-",
                    'productCode' => $kd,
                    'qty' => $qty_end[$i],
                    'UOM' => $uom_end[$i],
                    'price_per_UOM' => 0,
                    'note' => $note_end[$i] ?? null,
                    'product_status' => 'repack_end',
                ]);
            }

            DB::commit();
            session()->flash('msg', 'updated successfully');
            return redirect()->route("dashboard");
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('msg', 'ERROR: ' . $e->getMessage());
            return redirect()->route("dashboard");
        }
    } 

    public function amend_moving_data(Request $req)
    {
        $storageCodeSender = $req->storageCodeSender;
        $storageCodeReceiver = $req->storageCodeReceiver;
        $no_moving = $req->no_moving;
        $old_moving = $req->old_moving;
        $moving_date = $req->moving_date;

        $productCodes = $req->input('kd');
        $qtys = $req->input('qty');
        $uoms = $req->input('uom');
        $price_per_uom = $req->input("price_per_uom");
        $notes = $req->input('note');

        try {
            DB::beginTransaction();

            // Update moving number in related records
            $this->orderProductService->update_Moving($old_moving, $no_moving);

            // Update the moving record
            Moving::where("no_moving", $no_moving)->update([
                "storageCodeSender" => $storageCodeSender,
                "storageCodeReceiver" => $storageCodeReceiver,
                "no_moving" => $no_moving,
                "moving_date" => $moving_date,
            ]);

            // Process products
            if ($productCodes) {
                // Get existing product codes for this moving
                $existingProducts = Order_Product::where('moving_no_moving', $no_moving)
                    ->pluck('productCode')
                    ->toArray();

                // Update existing products
                foreach ($productCodes as $i => $productCode) {
                    $qty = $qtys[$i];
                    $uom = $uoms[$i];
                    $note = $notes[$i] ?? null;

                    Order_Product::where('moving_no_moving', $no_moving)
                        ->where('productCode', $productCode)
                        ->update([
                            'qty' => $qty,
                            'UOM' => $uom,
                            'note' => $note,
                        ]);
                }

                // Insert new products that don’t exist
                $newProducts = array_diff($productCodes, $existingProducts);
                foreach ($newProducts as $i => $productCode) {
                    Order_Product::create([
                        'nomor_surat_jalan' => '-',
                        'repack_no_repack' => '-',
                        'moving_no_moving' => $no_moving,
                        "PO_no_PO" => "-",
                        'productCode' => $productCode,
                        'qty' => $qtys[$i],
                        'UOM' => $uoms[$i],
                        'price_per_UOM' => $price_per_uom[$i] ?? 0,
                        'note' => $notes[$i] ?? null,
                        'product_status' => 'moving',
                    ]);
                }

                // Delete products that are no longer in the request
                $productsToDelete = array_diff($existingProducts, $productCodes);
                if (!empty($productsToDelete)) {
                    Order_Product::where('moving_no_moving', $no_moving)
                        ->whereIn('productCode', $productsToDelete)
                        ->delete();
                }
            } else {
                // If no products are provided, delete all products for this moving
                Order_Product::where('moving_no_moving', $no_moving)->delete();
            }

            DB::commit();
            session()->flash('msg', 'Updated successfully');
            return redirect()->route("dashboard");
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('msg', 'ERROR: ' . $e->getMessage());
            return redirect()->route("dashboard");
        }
    }

    public function amend_purchase_data(Request $req)
    {
        $no_PO = $req->no_PO; // The purchase order number to update
        $purchaseDate = $req->purchaseDate;
        $customerCode = $req->customerCode;

        $productCodes = $req->input('kd');
        $qtys = $req->input('qty');
        $uoms = $req->input('uom');
        $price_per_uom = $req->input('price_per_uom');
        $notes = $req->input('note');

        try {
            DB::beginTransaction();

            // Update the purchase order
            $purchaseOrder = Purchase_Order::where('no_PO', $no_PO)->firstOrFail();
            $purchaseOrder->update([
                'purchaseDate' => $purchaseDate,
                'customerCode' => $customerCode,
            ]);

            // Process products
            if ($productCodes) {
                // Get existing product codes for this purchase order
                $existingProducts = Order_Product::where('PO_no_PO', $no_PO)
                    ->pluck('productCode')
                    ->toArray();

                // Update existing products
                foreach ($productCodes as $i => $productCode) {
                    $qty = $qtys[$i];
                    $uom = $uoms[$i];
                    $price = $price_per_uom[$i];
                    $note = $notes[$i] ?? null;

                    Order_Product::where('PO_no_PO', $no_PO)
                        ->where('productCode', $productCode)
                        ->update([
                            'qty' => $qty,
                            'UOM' => $uom,
                            'price_per_UOM' => $price,
                            'note' => $note,
                        ]);
                }

                // Insert new products that don’t exist
                $newProducts = array_diff($productCodes, $existingProducts);
                foreach ($newProducts as $i => $productCode) {
                    Order_Product::create([
                        'nomor_surat_jalan' => '-',
                        'repack_no_repack' => '-',
                        'moving_no_moving' => '-',
                        'PO_no_PO' => $no_PO,
                        'productCode' => $productCode,
                        'qty' => $qtys[$i],
                        'UOM' => $uoms[$i],
                        'price_per_UOM' => $price_per_uom[$i],
                        'note' => $notes[$i] ?? null,
                        'product_status' => 'purchase_order',
                    ]);
                }

                // Delete products that are no longer in the request
                $productsToDelete = array_diff($existingProducts, $productCodes);
                if (!empty($productsToDelete)) {
                    Order_Product::where('PO_no_PO', $no_PO)
                        ->whereIn('productCode', $productsToDelete)
                        ->delete();
                }
            } else {
                // If no products are provided, delete all products for this purchase order
                Order_Product::where('PO_no_PO', $no_PO)->delete();
            }

            DB::commit();
            session()->flash('msg', 'Purchase order updated successfully');
            return redirect()->route("customer_dashboard");
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('msg', 'ERROR: ' . $e->getMessage());
            return redirect()->route("customer_dashboard");
        }
    }

    public function amend_delete_data(Request $req)
    {
        $data = $req->data;
        $code = $req->code;

        try {
            DB::beginTransaction();

            switch ($data) {
                case "slip":
                    DB::delete("DELETE FROM order_products WHERE nomor_surat_jalan = ?", [$code]);
                    DB::delete("DELETE FROM payments WHERE nomor_surat_jalan = ?", [$code]);
                    DB::delete("DELETE FROM invoices WHERE nomor_surat_jalan = ?", [$code]);
                    DB::delete("DELETE FROM orders WHERE nomor_surat_jalan = ?", [$code]);
                    break;
                case "invoice":
                    if (strpos($code, "SJP")) {
                        DB::update("UPDATE order_products SET price_per_UOM = 0 WHERE moving_no_moving = ?", [$code]);
                        DB::delete("DELETE FROM payments WHERE no_moving = ?", [$code]);
                        DB::delete("DELETE FROM invoices WHERE no_moving = ?", [$code]);
                    } else {
                        DB::update("UPDATE order_products SET price_per_UOM = 0 WHERE nomor_surat_jalan = ?", [$code]);
                        DB::delete("DELETE FROM payments WHERE nomor_surat_jalan = ?", [$code]);
                        DB::delete("DELETE FROM invoices WHERE nomor_surat_jalan = ?", [$code]);
                    }
                    break;
                case "payment":
                    DB::delete("DELETE FROM payments WHERE payment_id = ?", [$code]);
                    break;
                case "repack":
                    DB::delete("DELETE FROM order_products WHERE repack_no_repack = ?", [$code]);
                    DB::delete("DELETE FROM repacks WHERE no_repack = ?", [$code]);
                    break;
                case "moving":
                    DB::delete("DELETE FROM order_products WHERE moving_no_moving = ?", [$code]);
                    DB::delete("DELETE FROM movings WHERE no_moving = ?", [$code]);
                    break;
                case "purchase":
                    DB::delete("DELETE FROM order_products WHERE PO_no_PO = ?", [$code]);
                    DB::delete("DELETE FROM purchase_orders WHERE no_PO = ?", [$code]);
                    session()->flash('msg', 'Record deleted successfully');
                    return redirect()->route("customer_dashboard");
            }

            DB::commit();
            session()->flash('msg', 'Record deleted successfully');
            return redirect()->route("dashboard");
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('msg', 'ERROR: ' . $e->getMessage());
            return redirect()->route("dashboard");
        }
    }
}
