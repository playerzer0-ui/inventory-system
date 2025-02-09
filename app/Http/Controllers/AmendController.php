<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Order_Product;
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
}
