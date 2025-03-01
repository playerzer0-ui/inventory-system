<?php

namespace Tests\Feature;

use Database\Seeders\FakerData2025Seeder;
use Database\Seeders\NeutralValues;
use Database\Seeders\TestSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(NeutralValues::class);
        // $this->seed(TestSeed::class);
        // $this->seed(FakerData2025Seeder::class);
    }
}
