<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSKULegendDefinitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skulegend_definitions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku_legend_description',50)->nullable();
            $table->string('btb_segmentation',30)->nullable();
            $table->string('dw_segmentation',30)->nullable();
            $table->string('omg_segmentation',30)->nullable();
            $table->string('baseus_segmentation',30)->nullable();
            $table->string('districon_segmentation',30)->nullable();
            $table->string('distriout_segmentation',30)->nullable();
            $table->string('online_segmentation',30)->nullable();
            $table->string('guam_segmentation',30)->nullable();
            $table->text('sku_legend_definition')->nullable();
            $table->enum('sku_legend_status',['ACTIVE','INACTIVE'])->default('ACTIVE')->nullable();
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
        Schema::dropIfExists('skulegend_definitions');
    }
}
