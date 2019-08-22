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

        $nTable='grupos_permisos';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';
            $table->smallIncrements('id');
            $table->string('name',100);
            $table->char('status',1)->default('1');
            $table->timestamps();
        });

        $nTable='permisos';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';
            $table->smallIncrements('id');
            $table->string('name',100);
            $table->string('descrip')->Nullable();
            $table->char('status',1)->default('1');
            $table->timestamps();
            $table->smallInteger('fk_grupos_permisos')->unsigned();
            $table->foreign('fk_grupos_permisos')->references('id')->on('grupos_permisos')->onDelete('cascade')->onUpdate('cascade');

        });

        $nTable='roles';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';
            $table->smallIncrements('id');
            $table->string('name',100);
            $table->string('descrip')->Nullable();
            $table->char('status',1)->default('1');
            $table->timestamps();
        });

        $nTable='roles_permisos';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';
            $table->smallIncrements('id');
            $table->smallInteger('fk_roles')->unsigned();;
            $table->smallInteger('fk_permisos')->unsigned();;
            $table->foreign('fk_roles')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('fk_permisos')->references('id')->on('permisos')->onDelete('cascade')->onUpdate('cascade');
        });

        $nTable='usuarios';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';
            $table->smallIncrements('id');
            $table->string('name',100);
            $table->string('email',100);
            $table->string('pass',30);
            $table->char('activo',1)->default('0');
            $table->rememberToken();
            $table->char('status',1)->default('1');
            $table->smallInteger('rolActivo')->default(0);
            $table->timestamps();
        });

        $nTable='usuarios_permisos';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';
            $table->smallIncrements('id');
            $table->smallInteger('fk_usuarios')->unsigned();;
            $table->smallInteger('fk_permisos')->unsigned();;
            $table->foreign('fk_usuarios')->references('id')->on('usuarios')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('fk_permisos')->references('id')->on('permisos')->onDelete('cascade')->onUpdate('cascade');
        });

        $nTable='usuarios_roles';
        Schema::dropIfExists($nTable);
        Schema::create($nTable, function (Blueprint $table) {
            $table->engine ='InnoDB';
            $table->smallIncrements('id');
            $table->smallInteger('fk_usuarios')->unsigned();;
            $table->smallInteger('fk_roles')->unsigned();;
            $table->foreign('fk_usuarios')->references('id')->on('usuarios')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('fk_roles')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
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

        Schema::dropIfExists('grupos_permisos');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('roles_permisos');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('usuarios_permisos');
        Schema::dropIfExists('usuarios_roles');

        Schema::enableForeignKeyConstraints();

    }
}

