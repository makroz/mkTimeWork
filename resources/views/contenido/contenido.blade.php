    @extends('principal')
    @section('contenido')

    <template v-if="menu==0">
        <h1>Bienvenidos a  gFactura</h1>

    </template>

    <template v-if="menu==1">
        probandp
        <gpermisos-c></gpermisos-c>
    </template>

    <template v-if="menu==2">
    <h1>Contenido menu 2</h1>
        </template>

    <template v-if="menu==3">
    <h1>Contenido menu 3</h1>
    </template>

    @endsection
