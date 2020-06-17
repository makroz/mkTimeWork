<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModHorariosV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        $nTable='horarios';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';

            $table->increments('id');
            $table->string('name', 100);
            $table->tinyInteger('periodo_l')->default(0);

            $table->char('status', 1)->default('1');
            $table->timestamps();
            $table->softDeletes();
        });

        $nTable='empleados_horarios';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';

            $table->increments('id');


            $table->dateTime('fec_ini');
            $table->dateTime('fec_fin')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->integer('empleados_id')->unsigned();
            $table->foreign('empleados_id')->references('id')->on('empleados')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('horarios_id')->unsigned();
            $table->foreign('horarios_id')->references('id')->on('horarios')->onDelete('cascade')->onUpdate('cascade');

        });

        $nTable='dia_libre';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';

            $table->increments('id');
            $table->unsignedTinyInteger('valor')->default(0);

            $table->dateTime('fec_ini');
            $table->dateTime('fec_fin')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->integer('empleados_id')->unsigned();
            $table->foreign('empleados_id')->references('id')->on('empleados')->onDelete('cascade')->onUpdate('cascade');
        });


        $nTable='dias_horarios';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';

            $table->increments('id');

            $table->tinyInteger('ndias');
            $table->tinyInteger('turnos')->default(1);
            $table->timestamp('turno1')->nullable();
            $table->float('horas1',4,2)->nullable();
            $table->timestamp('turno2')->nullable();
            $table->float('horas2',4,2)->nullable();
            $table->timestamp('turno3')->nullable();
            $table->float('horas3',4,2)->nullable();

            $table->char('status', 1)->default('1');

            $table->integer('horarios_id')->unsigned();
            $table->foreign('horarios_id')->references('id')->on('horarios')->onDelete('cascade')->onUpdate('cascade');
        });

        $nTable='param_horarios';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';

            $table->increments('id');

            $table->tinyInteger('ndias');
            $table->tinyInteger('turnos')->default(1);
            $table->timestamp('turno1')->nullable();
            $table->float('horas1',4,2)->nullable();
            $table->timestamp('turno2')->nullable();
            $table->float('horas2',4,2)->nullable();
            $table->timestamp('turno3')->nullable();
            $table->float('horas3',4,2)->nullable();

            $table->char('status', 1)->default('1');

            $table->integer('horarios_id')->unsigned();
            $table->foreign('horarios_id')->references('id')->on('horarios')->onDelete('cascade')->onUpdate('cascade');
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
