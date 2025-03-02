<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Truck;
use App\Service\OrderProductService as ServiceOrderProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    protected $orderProductService;

    public function __construct(ServiceOrderProductService $orderProductService)
    {
        $this->orderProductService = $orderProductService;
    }

    public function getProductSuggestions(Request $req)
    {
        $code = $req->input("code");
        $products = Product::where('productCode', 'like', '%' . $code . '%')
        ->limit(10)
        ->pluck('productCode');

        return $products;
    }

    public function getProductDetails(Request $req)
    {
        $code = $req->input("code");
        $products = Product::where("productCode", $code)->first();
        
        return $products;
    }

    public function getOrderByNoSJ(Request $req)
    {
        $no_sj = $req->no_sj;

        return $this->orderProductService->getOrderByNoSJ($no_sj);
    }

    public function getOrderProducts(Request $req)
    {
        $no_sj = $req->no_sj;
        $status = $req->status;

        return $this->orderProductService->getOrderProducts($no_sj, $status);
    }

    public function getTruck(Request $req)
    {
        $qty = $req->qty;

        // Determine the truck size based on quantity
        $size = null;
        if ($qty <= 5000) {
            $size = 'S';
        } elseif ($qty <= 15000) {
            $size = 'M';
        } else {
            $size = 'L';
        }

        // Get counts of truck assignments from the orders table
        $truckWorkCounts = DB::table('orders')
            ->select('no_truk_out', DB::raw('COUNT(*) as total_count'))
            ->whereNotNull('no_truk_out')
            ->groupBy('no_truk_out')
            ->pluck('total_count', 'no_truk_out'); // Key-value pair: [no_truk_out => total_count]

        // Step 1: Find idle trucks (mode = 1) of the required size
        $trucks = Truck::where('mode', 1)
            ->where('size', $size)
            ->get();

        if ($trucks->isNotEmpty()) {
            // Assign the truck with the least work within the required size
            $selectedTruck = $this->findTruckWithLeastWork($trucks, $truckWorkCounts);
            return $selectedTruck;
        }

        // Step 2: If no truck of the required size, upsize to a larger truck
        $alternativeSizes = $this->getLargerSizes($size);
        $trucks = Truck::where('mode', 1)
            ->whereIn('size', $alternativeSizes)
            ->get();

        if ($trucks->isNotEmpty()) {
            // Assign the truck with the least work among the larger sizes
            $selectedTruck = $this->findTruckWithLeastWork($trucks, $truckWorkCounts);
            return $selectedTruck;
        }

        // Step 3: If no suitable truck is found, return a response
        return response()->json(['error' => 'No suitable truck available'], 404);
    }

    // Helper function to determine larger truck sizes
    private function getLargerSizes($size)
    {
        switch ($size) {
            case 'S':
                return ['M', 'L']; // Upsize to M or L
            case 'M':
                return ['L']; // Upsize to L
            case 'L':
                return []; // No larger sizes for L
            default:
                return [];
        }
    }

    // Helper function to find the truck with the least work
    private function findTruckWithLeastWork($trucks, $truckWorkCounts)
    {
        $selectedTruck = null;
        $leastWork = PHP_INT_MAX;

        foreach ($trucks as $truck) {
            $workCount = $truckWorkCounts[$truck->no_truk] ?? 0; // Get assigned work count or 0 if none
            if ($workCount < $leastWork) {
                $selectedTruck = $truck;
                $leastWork = $workCount;
            }
        }

        return $selectedTruck;
    }

    public function generate_LPB_SJK_INV(Request $req)
    {
        $state = $req->state;
        $storageCode = $req->storageCode;
        $month = $req->month;
        $year = $req->year;

        $uuid = substr(Str::uuid()->toString(), 0, 8);
        $timestamp = now()->format('YmdHis');

        $format = "$timestamp-$uuid/$state/$storageCode/$month/$year";

        return $format;
    }
}
