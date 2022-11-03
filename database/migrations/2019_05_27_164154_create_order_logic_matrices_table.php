<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderLogicMatricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_logic_matrices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('channels_id')->unsigned()->nullable();
            $table->integer('stores_id')->unsigned()->nullable();
            $table->integer('segmentation_id')->unsigned()->nullable();
            $table->integer('skulegend_id')->unsigned()->nullable();
            $table->text('order_logic')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
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
        Schema::dropIfExists('order_logic_matrices');
    }
}
