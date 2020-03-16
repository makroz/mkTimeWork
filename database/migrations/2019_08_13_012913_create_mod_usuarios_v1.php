<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use phpDocumentor\Reflection\Types\Nullable;

class CreateModUsuariosV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        $nTable='roles';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';
            $table->smallIncrements('id');
            $table->string('name', 20);
            $table->string('descrip', 200)->nullable();
            $table->char('status', 1)->default('1');
            $table->timestamps();
            $table->softDeletes();
        });

        $nTable='permisos';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';
            $table->increments('id');
            $table->string('slug', 20)->unique();
            $table->string('name', 100);

            $table->string('descrip', 200)->nullable();

            $table->char('status', 1)->default('1');
            $table->timestamps();
            $table->softDeletes();
            //$table->smallInteger('fk_grupos_permisos')->unsigned();
            //$table->foreign('fk_grupos_permisos')->references('id')->on('grupos_permisos')->onDelete('cascade')->onUpdate('cascade');
        });

        $nTable='grupos';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';
            $table->smallIncrements('id');
            $table->string('name', 100);
            $table->string('descrip', 200)->nullable();
            $table->char('status', 1)->default('1');
            $table->timestamps();
            $table->softDeletes();
        });


        $nTable='grupos_permisos';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';
//            $table->smallIncrements('id');
            $table->unsignedTinyInteger('valor')->default(0);
            $table->char('status', 1)->default('1');
//            $table->timestamps();
//            $table->softDeletes();
            $table->integer('permisos_id')->unsigned();
            $table->foreign('permisos_id')->references('id')->on('permisos')->onDelete('cascade')->onUpdate('cascade');
            $table->smallInteger('grupos_id')->unsigned();
            $table->foreign('grupos_id')->references('id')->on('grupos')->onDelete('cascade')->onUpdate('cascade');
//            $table>unique(['grupos_id', 'permisos_id']);
        });


        $nTable='usuarios';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';
            $table->increments('id');
            $table->string('name', 100);
            $table->string('email', 100)>unique();
            $table->string('pass', 30);
            $table->char('activo', 1)->default('0');
            $table->rememberToken();
            $table->char('status', 1)->default('1');
            $table->smallInteger('rolActivo')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->smallInteger('roles_id')->unsigned();
            $table->foreign('roles_id')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
        });


        $nTable='usuarios_permisos';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';
            //    $table->increments('id');
            $table->unsignedTinyInteger('valor')->default(0);
            $table->integer('usuarios_id')->unsigned();
            $table->foreign('usuarios_id')->references('id')->on('usuarios')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('permisos_id')->unsigned();
            $table->foreign('permisos_id')->references('id')->on('permisos')->onDelete('cascade')->onUpdate('cascade');
        });

        $nTable='usuarios_grupos';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';
//            $table->increments('id');
            $table->integer('usuarios_id')->unsigned();
            $table->foreign('usuarios_id')->references('id')->on('usuarios')->onDelete('cascade')->onUpdate('cascade');
            $table->smallInteger('grupos_id')->unsigned();
            $table->foreign('grupos_id')->references('id')->on('grupos')->onDelete('cascade')->onUpdate('cascade');
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

        Schema::dropIfExists('roles');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('grupos');
        Schema::dropIfExists('grupos_permisos');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('usuarios_permisos');
        Schema::dropIfExists('usuarios_grupos');

        Schema::enableForeignKeyConstraints();
    }
}
