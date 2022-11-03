<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetsInventoryHeader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets_inventory_header', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('invoice_no')->nullable();
            $table->date('rr_date')->nullable();
            $table->string('expiration_date')->nullable();
            $table->string('location')->nullable();
            $table->string('wattage')->nullable();
            $table->string('phase')->nullable();
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
        Schema::dropIfExists('assets_inventory_header');
    }
}
