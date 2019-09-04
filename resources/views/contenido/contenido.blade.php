    @extends('principal')
    @section('contenido')

    <template v-if="menu==0">
        <h1>Bienvenidos a  gFactura</h1>
    </template>

    <template v-if=" menu=='gruposPermisos' ">
        <gpermisos-c></gpermisos-c>
    </template>

    <template v-if="menu=='permisos'">
        <h1>Contenido menu <span v-text="menu"></span> </h1>
    </template>

    <template v-if="menu=='roles'">
        <h1>Contenido menu <span v-text="menu"></span> </h1>
    </template>

    @endsection
