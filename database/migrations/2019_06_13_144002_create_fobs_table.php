<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('FobID')->comment('realaccess.empdetails.empref');
            $table->integer('UserID')->comment('realaccess.empdetails.empref');
            $table->timestamp('date')->comment('Unnecessary - use created_at');
            $table->string('MachineID', 255);
            $table->boolean('deleted')->comment('Unnecessary - use deleted_at');
            $table->timestamps();
            $table->timestamp('deleted_at')->comment('[WARN] Not soft deletes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fobs');
    }
}
