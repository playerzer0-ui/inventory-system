<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test invoice in.
     *
     * @return void
     */
    public function test_create_invoice_in()
    {
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
            'invoice_date' => '2025-01-01',
            'no_invoice' => '12321213',
            'no_faktur' => 'factorCode1',
            'no_moving' => '-',
            'tax' => 11,
            'price_per_uom' => [999, 999],
        ];
        
        // Act: Perform the HTTP POST request
        $this->post(route('create_slip'), $data);
        $this->post(route("create_invoice"), $data);

        $this->assertDatabaseHas("invoices", [
            'nomor_surat_jalan' => $data["no_sj"],
            'invoice_date' => $data["invoice_date"],
            'no_invoice' => $data["no_invoice"],
            'no_faktur' => $data["no_faktur"],
            'tax' => $data["tax"],
        ]);

        for($i = 0; $i < count($data['kd']); $i++){
            $this->assertDatabaseHas('order_products', [
                'price_per_uom' => $data['price_per_uom'][$i],
                'product_status' => $data["pageState"]
            ]);
        }
    }

    public function test_create_invoice_out()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seed();

        $data = [
            'no_sj' => '20250119094600-27035867/LPB/APA/1/2025',
            'storageCode' => 'NON',
            'no_LPB' => 'dasasdasd',
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
            'invoice_date' => '2025-01-01',
            'no_invoice' => '12321213',
            'no_faktur' => 'factorCode1',
            'no_moving' => '-',
            'tax' => 11,
            'price_per_uom' => [999, 999],
        ];
        
        // Act: Perform the HTTP POST request
        $this->post(route('create_slip'), $data);
        $this->post(route("create_invoice"), $data);

        $this->assertDatabaseHas("invoices", [
            'nomor_surat_jalan' => $data["no_sj"],
            'invoice_date' => $data["invoice_date"],
            'no_invoice' => $data["no_invoice"],
            'no_faktur' => $data["no_faktur"],
            'tax' => $data["tax"],
        ]);

        for($i = 0; $i < count($data['kd']); $i++){
            $this->assertDatabaseHas('order_products', [
                'price_per_uom' => $data['price_per_uom'][$i],
                'product_status' => $data["pageState"]
            ]);
        }
    }
}
