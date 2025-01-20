<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Order_Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SlipControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * slip in.
     *
     * @return void
     */
    public function test_create_slip_in()
    {
        // Arrange: Define the input data
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seed();
        $data = [
            'no_sj' => 'H19000001',
            'storageCode' => 'APA',
            'no_LPB' => '20250119094600-27035867/LPB/APA/1/2025',
            'no_truk' => 'truck1',
            'vendorCode' => 'COC',
            'customerCode' => 'NON',
            'order_date' => '2025-01-19',
            'purchase_order' => '12345678',
            'status_mode' => '1',
            'kd' => ['MM-100-A', 'MM-120-A'],
            'qty' => [10, 20],
            'uom' => ['tray', 'tray'],
            'note' => ['Note 1', 'Note 2'],
            'pageState' => 'in',
        ];

        // Act: Perform the HTTP POST request
        $response = $this->post(route('create_slip'), $data);

        // Assert: Verify the order was created
        $this->assertDatabaseHas('orders', [
            'nomor_surat_jalan' => $data['no_sj'],
            'storageCode' => $data['storageCode'],
            'no_LPB' => $data['no_LPB'],
            'vendorCode' => $data['vendorCode'],
            'customerCode' => $data['customerCode'],
            'orderDate' => $data['order_date'],
            'purchase_order' => $data['purchase_order'],
            'status_mode' => $data['status_mode'],
        ]);

        // Assert: Verify the products were created
        for($i = 0; $i < count($data['kd']); $i++){
            $this->assertDatabaseHas('order_products', [
                'nomor_surat_jalan' => $data['no_sj'],
                'productCode' => $data['kd'][$i],
                'qty' => $data['qty'][$i],
                'UOM' => $data['uom'][$i],
                'note' => $data['note'][$i],
                'product_status' => $data['pageState'],
            ]);
        }

        // Assert: Verify the flash message and redirection
        $response->assertSessionHas('msg', 'no_SJ: ' . $data['no_sj']);
        $response->assertRedirect(route('invoice', ['state' => $data['pageState']]));
    }

    /**
     * slip out.
     *
     * @return void
     */
    public function test_create_slip_out()
    {
        // Arrange: Define the input data
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seed();
        $data = [
            'no_sj' => '20250119094600-27035867/SJK/NON/1/2025',
            'storageCode' => 'APA',
            'no_LPB' => '-',
            'no_truk' => 'truck1',
            'vendorCode' => 'NON',
            'customerCode' => 'TOM',
            'order_date' => '2025-01-19',
            'purchase_order' => '12345678',
            'status_mode' => '1',
            'kd' => ['MM-100-A', 'MM-120-A'],
            'qty' => [10, 20],
            'uom' => ['tray', 'tray'],
            'note' => ['Note 1', 'Note 2'],
            'pageState' => 'out',
        ];

        // Act: Perform the HTTP POST request
        $response = $this->post(route('create_slip'), $data);

        // Assert: Verify the order was created
        $this->assertDatabaseHas('orders', [
            'nomor_surat_jalan' => $data['no_sj'],
            'storageCode' => $data['storageCode'],
            'no_LPB' => $data['no_LPB'],
            'vendorCode' => $data['vendorCode'],
            'customerCode' => $data['customerCode'],
            'orderDate' => $data['order_date'],
            'purchase_order' => $data['purchase_order'],
            'status_mode' => $data['status_mode'],
        ]);

        // Assert: Verify the products were created
        for($i = 0; $i < count($data['kd']); $i++){
            $this->assertDatabaseHas('order_products', [
                'nomor_surat_jalan' => $data['no_sj'],
                'productCode' => $data['kd'][$i],
                'qty' => $data['qty'][$i],
                'UOM' => $data['uom'][$i],
                'note' => $data['note'][$i],
                'product_status' => $data['pageState'],
            ]);
        }

        // Assert: Verify the flash message and redirection
        $response->assertSessionHas('msg', 'no_SJ: ' . $data['no_sj']);
        $response->assertRedirect(route('invoice', ['state' => $data['pageState']]));
    }
}
