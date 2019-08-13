<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbMonitoringData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_monitoring_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('temperature');
            $table->float('ph');
            $table->float('turbidity');
            $table->text('status');
            $table->text('information');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_monitoring_data');
    }
}
