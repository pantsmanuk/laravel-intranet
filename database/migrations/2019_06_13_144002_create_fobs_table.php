<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realaccess_fob_lookup', function (Blueprint $table) {
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
        Schema::dropIfExists('realaccess_fob_lookup');
    }
}
