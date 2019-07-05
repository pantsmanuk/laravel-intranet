<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelephonesTable extends Migration
{
    /**
     * Run the migrations.
     * @todo Table can be "telephones" once we deprecate ci-intranet
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telephone', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 16);
            $table->string('number', 16);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @todo Table can be "telephones" once we deprecate ci-intranet
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telephone');
    }
}
