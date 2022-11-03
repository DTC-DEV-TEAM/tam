<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('purchase_headers_id')->unsigned();
            $table->string('digits_code',60)->nullable();
            $table->string('upc_code',50)->nullable();
            $table->string('upc_code2',50)->nullable();
            $table->string('upc_code3',50)->nullable();
            $table->string('upc_code4',50)->nullable();
            $table->string('upc_code5',50)->nullable();
            $table->string('supplier_itemcode',50)->nullable();
            $table->string('item_description',100)->nullable();
            $table->decimal('dtp_rf', 18, 2)->nullable();
            $table->integer('skustatus_id')->unsigned();
            $table->integer('quantity_pre_ordered',false,true)->length(10)->nullable();
            $table->integer('quantity_ordered',false,true)->length(10)->nullable();
            $table->integer('quantity_onhand',false,true)->length(10)->nullable();
            $table->integer('quantity_reservable',false,true)->length(10)->nullable();
            $table->integer('quantity_reorder',false,true)->length(10)->nullable();
            $table->integer('quantity_reserved',false,true)->length(10)->nullable();
            $table->integer('quantity_received',false,true)->length(10)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_lines');
    }
}
