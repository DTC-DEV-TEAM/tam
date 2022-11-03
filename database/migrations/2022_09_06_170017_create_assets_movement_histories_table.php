<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetsMovementHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets_movement_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('header_id')->nullable();
            $table->integer('body_id')->nullable();
            $table->datetime('date_update')->nullable();
            $table->string('history_updated_by')->nullable();
            $table->string('description')->nullable();
            $table->string('deployed_to')->nullable();
            $table->string('location')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('assets_movement_histories');
    }
}
