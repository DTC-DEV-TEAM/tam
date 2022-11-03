<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetsInventoryBody extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets_inventory_body', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('header_id')->nullable();
            $table->integer('item_id')->nullable();
            $table->integer('statuses_id')->nullable();
            $table->string('digits_code')->nullable();
            $table->string('deployed_to')->nullable();
            $table->string('location')->nullable();
            $table->string('item_description')->nullable();
            $table->decimal('value', 18, 2)->nullable();
            $table->string('item_type')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('warranty_coverage')->nullable();
            $table->string('item_photo')->nullable();
            $table->string('asset_code')->nullable();
            $table->string('barcode')->nullable();
            $table->string('item_condition')->nullable();
            $table->string('item_category')->nullable();
            $table->string('transaction_per_asset')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->datetime('date_updated')->nullable();
            $table->datetime('archived')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assets_inventory_body');
    }
}
