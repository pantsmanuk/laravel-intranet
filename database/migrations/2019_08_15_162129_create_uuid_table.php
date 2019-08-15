<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUuidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::connection()->setSchemaGrammar(new \App\MySqlGrammar());

        Blueprint::macro('realBinary', function ($column, $length) {
            return $this->addColumn('realBinary', $column, compact('length'));
        });

        Schema::create('uuid', function (Blueprint $table) {
            $table->realBinary('id_bin', 16);
            $table->string('id_text', 36)
                ->virtualAs('(insert(insert(insert(insert(hex(`id_bin`),9,0,\'-\'),14,0,\'-\'),19,0,\'-\'),24,0,\'-\')) ')
                ->nullable();
            $table->bigInteger('id')
                ->nullable();

            $table->primary('id_bin');
            $table->unique('id_text');
            $table->unique('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uuid_test');
    }
}
