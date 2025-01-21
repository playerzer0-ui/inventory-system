<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid("userID")->primary();
            $table->string("email", 255);
            $table->string("password", 255);
            $table->integer("userType");
            }
        );

        Schema::create('products', function (Blueprint $table) {
            $table->string("productCode", 50)->primary();
            $table->string("productName", 100);
            }
        );

        Schema::create('customers', function (Blueprint $table) {
            $table->string("customerCode", 5)->primary();
            $table->string("customerName", 100);
            $table->string("customerAddress", 100);
            $table->string("customerNPWP", 100);
            }
        );

        Schema::create('vendors', function (Blueprint $table) {
            $table->string("vendorCode", 5)->primary();
            $table->string("vendorName", 100);
            $table->string("vendorAddress", 100);
            $table->string("vendorNPWP", 100);
            }
        );

        Schema::create('storages', function (Blueprint $table) {
            $table->string("storageCode", 5)->primary();
            $table->string("storageName", 100);
            $table->string("storageAddress", 100);
            $table->string("storageNPWP", 100);
            }
        );

        Schema::create('orders', function (Blueprint $table) {
            $table->string("nomor_surat_jalan", 100)->primary();
            $table->string("storageCode", 5);
            $table->string("no_LPB", 100);
            $table->string("no_truk", 100);
            $table->string("vendorCode", 5);
            $table->string("customerCode", 5);
            $table->date("orderDate")->nullable();
            $table->string("purchase_order", 30);
            $table->integer("status_mode");
            $table->foreign('storageCode')->references('storageCode')->on('storages')->onDelete('cascade');
            $table->foreign('vendorCode')->references('vendorCode')->on('vendors')->onDelete('cascade');
            $table->foreign('customerCode')->references('customerCode')->on('customers')->onDelete('cascade');
        });
        
        Schema::create('movings', function(Blueprint $table) {
            $table->string("no_moving", 100)->primary();
            $table->date("moving_date")->nullable();
            $table->string("storageCodeSender", 5);
            $table->string("storageCodeReceiver", 5);
            $table->foreign('storageCodeSender')->references('storageCode')->on('storages')->onDelete('cascade');
            $table->foreign('storageCodeReceiver')->references('storageCode')->on('storages')->onDelete('cascade');
        });
        
        Schema::create('invoices', function(Blueprint $table){
            $table->string("nomor_surat_jalan", 100);
            $table->string("no_moving", 100)->nullable();
            $table->date("invoice_date");
            $table->string("no_invoice", 100);
            $table->string("no_faktur", 100);
            $table->double("tax");
            $table->foreign('nomor_surat_jalan')->references('nomor_surat_jalan')->on('orders')->onDelete('cascade');
            $table->foreign('no_moving')->references('no_moving')->on('movings')->onDelete('cascade');
        });
        
        Schema::create('payments', function(Blueprint $table){
            $table->string("nomor_surat_jalan", 100);
            $table->string("no_moving", 100)->nullable();
            $table->date("payment_date");
            $table->double("payment_amount");
            $table->foreign('nomor_surat_jalan')->references('nomor_surat_jalan')->on('invoices')->onDelete('cascade');
            $table->foreign('no_moving')->references('no_moving')->on('movings')->onDelete('cascade');
        });
        
        Schema::create('repacks', function(Blueprint $table){
            $table->string("no_repack", 100)->primary();
            $table->date("repack_date")->nullable();
            $table->string("storageCode", 5);
            $table->foreign('storageCode')->references('storageCode')->on('storages')->onDelete('cascade');
        });        

        Schema::create('saldos', function(Blueprint $table){
            $table->string("productCode", 5);
            $table->string("storageCode", 5);
            $table->integer("totalQty");
            $table->double("price_per_qty");
            $table->integer("saldoMonth");
            $table->integer("saldoYear");
            $table->integer("saldoCount");
            $table->foreign('productCode')->references('productCode')->on('products')->onDelete('cascade');
            $table->foreign('storageCode')->references('storageCode')->on('storages')->onDelete('cascade');
        });

        Schema::create('order_products', function(Blueprint $table){
            $table->string("nomor_surat_jalan", 100);
            $table->string("repack_no_repack", 100);
            $table->string("moving_no_moving", 100);
            $table->string("productCode", 50);
            $table->integer("qty");
            $table->string("UOM", 20);
            $table->double("price_per_UOM");
            $table->string("note", 100)->nullable();
            $table->string("product_status", 30);
            $table->foreign('nomor_surat_jalan')->references('nomor_surat_jalan')->on('orders')->onDelete('cascade');
            $table->foreign('repack_no_repack')->references('no_repack')->on('repacks')->onDelete('cascade');
            $table->foreign('moving_no_moving')->references('no_moving')->on('movings')->onDelete('cascade');
            $table->foreign('productCode')->references('productCode')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
