<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('suppliers_id')->unsigned();
            $table->longText('comments')->nullable();
            $table->dateTime('order_date');
            $table->string('po_reference',11);
            $table->integer('groups_id')->unsigned();
            $table->integer('channels_id')->unsigned();
            $table->integer('stores_id')->unsigned();
            $table->decimal('total_amount', 18, 2)->nullable();
            $table->text('excel_file')->nullable();
            $table->enum('approval_status', [0,1,2,3])->default(0); //[0-pending, 1-approved, 2-rejected, 3-expired]
            $table->dateTime('approved_at')->nullable();
            $table->integer('approved_by')->unsigned()->nullable();
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
        Schema::dropIfExists('purchase_headers');
    }
}
