<?php 

namespace App\Service;

use App\Models\Order;
use App\Models\Order_Product;
use DateTime;
use Illuminate\Support\Facades\DB;

class StorageReport {

    public function getDebtReport($storageCode, $month, $year)
    {
        $debtDetails = Order::query()
            ->select([
                'orders.nomor_surat_jalan',
                'invoices.invoice_date',
                'invoices.no_invoice',
                'invoices.tax',
                'vendors.vendorName',
                DB::raw('COALESCE(payments.payment_date, "-") AS payment_date'),
                DB::raw('COALESCE(payments.payment_amount, 0) AS payment_amount'),
            ])
            ->join('invoices', 'orders.nomor_surat_jalan', '=', 'invoices.nomor_surat_jalan')
            ->join('vendors', 'orders.vendorCode', '=', 'vendors.vendorCode')
            ->leftJoin('payments', 'orders.nomor_surat_jalan', '=', 'payments.nomor_surat_jalan')
            ->whereMonth('invoices.invoice_date', $month)
            ->whereYear('invoices.invoice_date', $year)
            ->where('orders.storageCode', $storageCode)
            ->where('orders.status_mode', 1)
            ->get();

        $groupData = [];
        foreach ($debtDetails as $details) {
            $hutangKey = $details["nomor_surat_jalan"];
            if (!isset($groupData[$hutangKey])) {
                $groupData[$hutangKey] = [
                    "invoice_date" => $details["invoice_date"],
                    "no_invoice" => $details["no_invoice"],
                    "tax" => $details["tax"],
                    "vendorName" => $details["vendorName"],
                    "payments" => [],
                    "products" => []
                ];

                $productsList = Order_Product::where('nomor_surat_jalan', $hutangKey)
                    ->select([
                        'productCode',
                        'qty',
                        'price_per_UOM',
                        DB::raw('(qty * price_per_UOM) AS nominal')
                    ])
                    ->get();

                foreach ($productsList as $product) {
                    array_push($groupData[$hutangKey]["products"], [
                        "productCode" => $product["productCode"],
                        "qty" => $product["qty"],
                        "price_per_UOM" => $product["price_per_UOM"],
                        "nominal" => $product["nominal"]
                    ]);
                }
            }

            array_push($groupData[$hutangKey]["payments"], [
                "payment_date" => $details["payment_date"],
                "payment_amount" => $details["payment_amount"]
            ]);
        }

        return array_values($groupData);
    }

    public function getreceivablesReport($month, $year)
    {
        $storageCode = "NON";

        $receivablesDetails = Order::query()
            ->select([
                'orders.nomor_surat_jalan',
                'invoices.invoice_date',
                'invoices.no_invoice',
                'invoices.tax',
                'customers.customerName',
                DB::raw('COALESCE(payments.payment_date, "-") AS payment_date'),
                DB::raw('COALESCE(payments.payment_amount, 0) AS payment_amount'),
            ])
            ->join('invoices', 'orders.nomor_surat_jalan', '=', 'invoices.nomor_surat_jalan')
            ->join('customers', 'orders.customerCode', '=', 'customers.customerCode')
            ->leftJoin('payments', 'orders.nomor_surat_jalan', '=', 'payments.nomor_surat_jalan')
            ->whereMonth('invoices.invoice_date', $month)
            ->whereYear('invoices.invoice_date', $year)
            ->where('orders.storageCode', $storageCode)
            ->where('orders.status_mode', 2)
            ->get();

        $groupData = [];
        foreach ($receivablesDetails as $details) {
            $receivablesKey = $details["nomor_surat_jalan"];
            if (!isset($groupData[$receivablesKey])) {
                $groupData[$receivablesKey] = [
                    "invoice_date" => $details["invoice_date"],
                    "no_invoice" => $details["no_invoice"],
                    "tax" => $details["tax"],
                    "customerName" => $details["customerName"],
                    "payments" => [],
                    "products" => []
                ];

                $productsList = Order_Product::where('nomor_surat_jalan', $receivablesKey)
                    ->select([
                        'productCode',
                        'qty',
                        'price_per_UOM',
                        DB::raw('(qty * price_per_UOM) AS nominal')
                    ])
                    ->get();

                foreach ($productsList as $product) {
                    array_push($groupData[$receivablesKey]["products"], [
                        "productCode" => $product["productCode"],
                        "qty" => $product["qty"],
                        "price_per_UOM" => $product["price_per_UOM"],
                        "nominal" => $product["nominal"]
                    ]);
                }
            }

            array_push($groupData[$receivablesKey]["payments"], [
                "payment_date" => $details["payment_date"],
                "payment_amount" => $details["payment_amount"]
            ]);
        }

        return array_values($groupData);
    }

    public function getAllProductsMovingSaldo($storageCode, $month, $year, $productCode = null)
    {
        // Query for storageCodeSender
        $senders = DB::table('products as p')
            ->join('order_products as op', 'p.productCode', '=', 'op.productCode')
            ->join('movings as m', 'op.moving_no_moving', '=', 'm.no_moving')
            ->select(
                'p.productCode',
                'p.productName',
                'm.storageCodeSender as storageCode',
                DB::raw('MONTH(m.moving_date) as saldoMonth'),
                DB::raw('YEAR(m.moving_date) as saldoYear'),
                DB::raw('SUM(op.qty) as totalQty'),
                DB::raw('AVG(op.price_per_UOM) as avgPrice'),
                'op.product_status'
            )
            ->where('m.storageCodeSender', $storageCode)
            ->whereMonth('m.moving_date', $month)
            ->whereYear('m.moving_date', $year)
            ->when($productCode, function ($query) use ($productCode) {
                $query->where('p.productCode', $productCode);
            })
            ->groupBy(
                'p.productCode',
                'p.productName',
                'm.storageCodeSender',
                'saldoMonth',
                'saldoYear',
                'op.product_status'
            )
            ->get();

        // Query for storageCodeReceiver
        $receivers = DB::table('products as p')
            ->join('order_products as op', 'p.productCode', '=', 'op.productCode')
            ->join('movings as m', 'op.moving_no_moving', '=', 'm.no_moving')
            ->select(
                'p.productCode',
                'p.productName',
                'm.storageCodeReceiver as storageCode',
                DB::raw('MONTH(m.moving_date) as saldoMonth'),
                DB::raw('YEAR(m.moving_date) as saldoYear'),
                DB::raw('SUM(op.qty) as totalQty'),
                DB::raw('AVG(op.price_per_UOM) as avgPrice'),
                'op.product_status'
            )
            ->where('m.storageCodeReceiver', $storageCode)
            ->whereMonth('m.moving_date', $month)
            ->whereYear('m.moving_date', $year)
            ->when($productCode, function ($query) use ($productCode) {
                $query->where('p.productCode', $productCode);
            })
            ->groupBy(
                'p.productCode',
                'p.productName',
                'm.storageCodeReceiver',
                'saldoMonth',
                'saldoYear',
                'op.product_status'
            )
            ->get();

        return [$senders, $receivers];
    }

    public function getAllProductsForSaldo($storageCode, $month, $year, $productCode = null)
    {
        $storageCondition = $storageCode !== "NON";

        // Query for invoices
        $invoicesQuery = DB::table('products as p')
            ->join('order_products as op', 'p.productCode', '=', 'op.productCode')
            ->join('orders as o', 'op.nomor_surat_jalan', '=', 'o.nomor_surat_jalan')
            ->join('invoices as i', 'o.nomor_surat_jalan', '=', 'i.nomor_surat_jalan')
            ->select(
                'p.productCode',
                'p.productName',
                'o.storageCode',
                DB::raw('MONTH(i.invoice_date) as saldoMonth'),
                DB::raw('YEAR(i.invoice_date) as saldoYear'),
                DB::raw('SUM(op.qty) as totalQty'),
                DB::raw('ROUND(SUM(op.price_per_UOM * op.qty) / SUM(op.qty)) as avgPrice'),
                'op.product_status'
            )
            ->whereMonth('i.invoice_date', $month)
            ->whereYear('i.invoice_date', $year)
            ->when($storageCondition, function ($query) use ($storageCode) {
                $query->where('o.storageCode', $storageCode);
            })
            ->when($productCode, function ($query) use ($productCode) {
                $query->where('p.productCode', $productCode);
            })
            ->where(function ($query) use ($storageCode) {
                if ($storageCode === "NON") {
                    $query->where('op.product_status', '!=', 'out_tax')
                        ->where('op.product_status', 'NOT LIKE', '%repack%');
                } else {
                    $query->where('op.product_status', '!=', 'out');
                }
            })
            ->groupBy(
                'p.productCode',
                'p.productName',
                'o.storageCode',
                'saldoMonth',
                'saldoYear',
                'op.product_status'
            );

        // Query for repacks
        $repackQuery = DB::table('products as p')
            ->join('order_products as op', 'p.productCode', '=', 'op.productCode')
            ->join('repacks as r', 'op.repack_no_repack', '=', 'r.no_repack')
            ->select(
                'p.productCode',
                'p.productName',
                'r.storageCode',
                DB::raw('MONTH(r.repack_date) as saldoMonth'),
                DB::raw('YEAR(r.repack_date) as saldoYear'),
                DB::raw('SUM(op.qty) as totalQty'),
                DB::raw('ROUND(SUM(op.price_per_UOM * op.qty) / SUM(op.qty)) as avgPrice'),
                'op.product_status'
            )
            ->whereMonth('r.repack_date', $month)
            ->whereYear('r.repack_date', $year)
            ->when($storageCondition, function ($query) use ($storageCode) {
                $query->where('r.storageCode', $storageCode);
            })
            ->when($productCode, function ($query) use ($productCode) {
                $query->where('p.productCode', $productCode);
            })
            ->groupBy(
                'p.productCode',
                'p.productName',
                'r.storageCode',
                'saldoMonth',
                'saldoYear',
                'op.product_status'
            );

        // Combine with UNION ALL
        $results = $invoicesQuery->unionAll($repackQuery)
            ->orderByRaw("CASE 
                WHEN product_status = 'in' THEN 1
                WHEN product_status = 'repack_start' THEN 2
                WHEN product_status = 'repack_end' THEN 3
                " . ($storageCode !== "NON" ? "WHEN product_status = 'out_tax' THEN 4" : "WHEN product_status = 'out' THEN 4") . "
                ELSE 5
            END")
            ->get();

        // Include moving data
        $movings = $this->getAllProductsMovingSaldo($storageCode, $month, $year, $productCode);

        return [$results, $movings];
    }

    function getSaldoAwal($storageCode, $month, $year)
    {
        $data = [];

        // Querying the `saldos` table using Eloquent or DB Query Builder
        $results = DB::table('saldos')
            ->where('storageCode', $storageCode)
            ->where('saldoMonth', $month)
            ->where('saldoYear', $year)
            ->get();

        // Transform results into the desired structure
        foreach ($results as $key) {
            $data[$key->productCode] = [
                "storageCode" => $key->storageCode,
                "totalQty" => $key->totalQty,
                "totalPrice" => $key->totalPrice
            ];
        }

        return $data;
    }

    function updateBalance($productCode, $storageCode, $month, $year, $qty, $price)
    {
        // Check if the record exists
        $exist = DB::table('saldos')
            ->where('productCode', $productCode)
            ->where('storageCode', $storageCode)
            ->where('saldoMonth', $month)
            ->where('saldoYear', $year)
            ->exists();

        if ($exist) {
            // Update the existing record
            DB::table('saldos')
                ->where('productCode', $productCode)
                ->where('storageCode', $storageCode)
                ->where('saldoMonth', $month)
                ->where('saldoYear', $year)
                ->update([
                    'totalQty' => $qty,
                    'totalPrice' => $price
                ]);
        } else {
            // Insert a new record
            DB::table('saldos')->insert([
                'productCode' => $productCode,
                'storageCode' => $storageCode,
                'totalQty' => $qty,
                'totalPrice' => $price,
                'saldoMonth' => $month,
                'saldoYear' => $year
            ]);
        }
    }

    public function generateSaldo($storageCode, $month, $year, $productCode = null) {

        $storageReport = $this->getAllProductsForSaldo($storageCode, $month, $year, $productCode);
        $inouts = $storageReport[0];
        $movings = $storageReport[1];
        $date = new DateTime($year . "-" . $month . "-" . "01");
        $date->modify('-1 month');
        $prevMonth = $date->format('m');
        $prevYear = $date->format('Y');
        $data = [];
    
        $initial_balance = $this->getSaldoAwal($storageCode, $prevMonth, $prevYear);
        array_push($data, ["storageCode" => $storageCode, "month" => $month, "year" => $year]);
    
        foreach ($inouts as $key) {
            $productCode = $key->productCode;
            if (!isset($data[$productCode])) {
                $data[$productCode] = $this->initializeProductData($productCode, $key->productName);
            }
    
            $this->updateInitialBalance($data[$productCode], $initial_balance);
    
            switch ($key->product_status) {
                case "in":
                    $this->updateIn($data[$productCode], $key, "purchase");
                    break;
    
                case "out":
                    $this->updateOut($data[$productCode], $key, "sales");
                    break;
    
                case "out_tax":
                    $this->updateOut($data[$productCode], $key, "sales");
                    break;
    
                case "repack_start":
                    $this->updateOut($data[$productCode], $key, "repackOut");
                    break;
    
                case "repack_end":
                    $this->updateIn($data[$productCode], $key, "repackIn");
                    break;
            }
    
            $this->updateReadyToSellItems($data[$productCode]);
            $this->updateFinalBalance($data[$productCode]);
    
            $this->updateBalance($productCode, $storageCode, $month, $year, $data[$productCode]["final_balance"]["totalQty"], $data[$productCode]["final_balance"]["totalPrice"]);
        }
    
        foreach ($movings[1] as $key) {
            $productCode = $key->productCode;
            if (!isset($data[$productCode])) {
                $data[$productCode] = $this->initializeProductData($productCode, $key->productName);
            }
    
            $this->updateInitialBalance($data[$productCode], $initial_balance);
    
            // Handle movingIn logic here
            $this->updateIn($data[$productCode], $key, "movingIn");
    
            $this->updateReadyToSellItems($data[$productCode]);
            $this->updateFinalBalance($data[$productCode]);
    
            $this->updateBalance($productCode, $storageCode, $month, $year, $data[$productCode]["final_balance"]["totalQty"], $data[$productCode]["final_balance"]["totalPrice"]);
        }
    
        foreach ($movings[0] as $key) {
            $productCode = $key->productCode;
            if (!isset($data[$productCode])) {
                $data[$productCode] = $this->initializeProductData($productCode, $key->productName);
            }
    
            $this->updateInitialBalance($data[$productCode], $initial_balance);
    
            // Handle movingOut logic here
            $this->updateOut($data[$productCode], $key, "movingOut");
    
            $this->updateReadyToSellItems($data[$productCode]);
            $this->updateFinalBalance($data[$productCode]);
    
            $this->updateBalance($productCode, $storageCode, $month, $year, $data[$productCode]["final_balance"]["totalQty"], $data[$productCode]["final_balance"]["totalPrice"]);
        }
    
        return $data;
    }
    
    public function initializeProductData($productCode, $productName) {
        return [
            "productCode" => $productCode,
            "productName" => $productName,
            "initial_balance" => ["totalQty" => 0, "price_per_qty" => 0, "totalPrice" => 0],
            "in" => [
                "purchase" => ["totalQty" => 0, "price_per_qty" => 0, "totalPrice" => 0],
                "repackIn" => ["totalQty" => 0, "price_per_qty" => 0, "totalPrice" => 0],
                "movingIn" => ["totalQty" => 0, "price_per_qty" => 0, "totalPrice" => 0],
                "totalIn" => ["totalQty" => 0, "price_per_qty" => 0, "totalPrice" => 0]
            ],
            "out" => [
                "sales" => ["totalQty" => 0, "price_per_qty" => 0, "totalPrice" => 0],
                "repackOut" => ["totalQty" => 0, "price_per_qty" => 0, "totalPrice" => 0],
                "movingOut" => ["totalQty" => 0, "price_per_qty" => 0, "totalPrice" => 0],
                "totalOut" => ["totalQty" => 0, "price_per_qty" => 0, "totalPrice" => 0]
            ],
            "ready_to_sell_items" => ["totalQty" => 0, "price_per_qty" => 0, "totalPrice" => 0],
            "final_balance" => ["totalQty" => 0, "price_per_qty" => 0, "totalPrice" => 0]
        ];
    }
    
    public function updateInitialBalance(&$productData, $initial_balance) {
        $productCode = $productData["productCode"];
        if (isset($initial_balance[$productCode])) {
            $productData["initial_balance"]["totalQty"] = $initial_balance[$productCode]["totalQty"];
            $productData["initial_balance"]["totalPrice"] = $initial_balance[$productCode]["totalPrice"];
            
            if ($initial_balance[$productCode]["totalQty"] > 0) {
                $productData["initial_balance"]["price_per_qty"] = $initial_balance[$productCode]["totalPrice"] / $initial_balance[$productCode]["totalQty"];
            } else {
                $productData["initial_balance"]["price_per_qty"] = 0; // or handle this case as needed
            }
        }
    }
    
    public function updateIn(&$productData, $key, $type) {
        global $global_repackOut_price_per_qty;
    
        $productData["in"][$type]["totalQty"] = $key->totalQty;
        $productData["in"][$type]["price_per_qty"] = $key->avgPrice;
        $productData["in"][$type]["totalPrice"] = $key->totalQty * $key->avgPrice;
    
        if ($type == "repackIn") {
            $productData["in"][$type]["price_per_qty"] = $global_repackOut_price_per_qty;
            $productData["in"][$type]["totalPrice"] = $key->totalQty * $global_repackOut_price_per_qty;
        }
    
        $productData["in"]["totalIn"]["totalQty"] += $key->totalQty;
        $productData["in"]["totalIn"]["totalPrice"] += $productData["in"][$type]["totalPrice"];
        
        if ($productData["in"]["totalIn"]["totalQty"] > 0) {
            $productData["in"]["totalIn"]["price_per_qty"] = $productData["in"]["totalIn"]["totalPrice"] / $productData["in"]["totalIn"]["totalQty"];
        } else {
            $productData["in"]["totalIn"]["price_per_qty"] = 0;
        }
    }
    
    public function updateOut(&$productData, $key, $type) {
        global $global_repackOut_price_per_qty;
    
        $price_per_qty = $productData["ready_to_sell_items"]["price_per_qty"];
    
        $productData["out"][$type]["totalQty"] = $key->totalQty;
        $productData["out"][$type]["price_per_qty"] = $price_per_qty;
        $productData["out"][$type]["totalPrice"] = $key->totalQty * $price_per_qty;
    
        $global_repackOut_price_per_qty = $productData["out"][$type]["price_per_qty"];
    
        $productData["out"]["totalOut"]["totalQty"] += $key->totalQty;
        $productData["out"]["totalOut"]["totalPrice"] += $productData["out"][$type]["totalPrice"];
        
        if ($productData["out"]["totalOut"]["totalQty"] > 0) {
            $productData["out"]["totalOut"]["price_per_qty"] = $productData["out"]["totalOut"]["totalPrice"] / $productData["out"]["totalOut"]["totalQty"];
        } else {
            $productData["out"]["totalOut"]["price_per_qty"] = 0;
        }
    }
    
    public function updateReadyToSellItems(&$productData) {
        $productData["ready_to_sell_items"]["totalQty"] = $productData["in"]["totalIn"]["totalQty"] + $productData["initial_balance"]["totalQty"];
        $productData["ready_to_sell_items"]["totalPrice"] = $productData["in"]["totalIn"]["totalPrice"] + $productData["initial_balance"]["totalPrice"];
        
        if ($productData["ready_to_sell_items"]["totalQty"] > 0) {
            $productData["ready_to_sell_items"]["price_per_qty"] = $productData["ready_to_sell_items"]["totalPrice"] / $productData["ready_to_sell_items"]["totalQty"];
        } else {
            $productData["ready_to_sell_items"]["price_per_qty"] = 0;
        }
    }
    
    public function updateFinalBalance(&$productData) {
        $productData["final_balance"]["totalQty"] = $productData["ready_to_sell_items"]["totalQty"] - $productData["out"]["totalOut"]["totalQty"];
        $productData["final_balance"]["totalPrice"] = $productData["ready_to_sell_items"]["totalPrice"] - $productData["out"]["totalOut"]["totalPrice"];
        
        if ($productData["final_balance"]["totalQty"] > 0) {
            $productData["final_balance"]["price_per_qty"] = $productData["final_balance"]["totalPrice"] / $productData["final_balance"]["totalQty"];
        } else {
            $productData["final_balance"]["price_per_qty"] = 0;
        }
    }
}

?>