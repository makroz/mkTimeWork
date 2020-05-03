<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModEmpresasV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        $nTable='empresas';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';

            $table->increments('id');
            $table->string('name', 40);
            $table->string('email', 200)->nullable();

            $table->char('status', 1)->default('1');
            $table->timestamps();
            $table->softDeletes();
        });

        $nTable='sucursales';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';

            $table->increments('id');
            $table->string('name', 100);
            $table->string('dir',200)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('tel',50)->nullable();

            $table->char('status', 1)->default('1');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('empresas_id')->unsigned();
            $table->foreign('empresas_id')->references('id')->on('empresas')->onDelete('cascade')->onUpdate('cascade');
        });

        $nTable='empleados';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';

            $table->increments('id');
            $table->string('name', 100);
            $table->string('ci', 100);
            $table->string('email', 100)->nullable();
            $table->string('tel', 50)->nullable();
            $table->string('dir', 150)->nullable();

            $table->char('status', 1)->default('1');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('sucursales_id')->unsigned();
            $table->foreign('sucursales_id')->references('id')->on('sucursales')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('empresas');
        Schema::dropIfExists('sucursales');
        Schema::dropIfExists('empleados');

        Schema::enableForeignKeyConstraints();
    }
}
