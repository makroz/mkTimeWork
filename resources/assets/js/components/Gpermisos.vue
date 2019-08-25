<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
      <li class="breadcrumb-item">Home</li>
      <li class="breadcrumb-item">
        <a href="#">Admin</a>
      </li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
    <div class="container-fluid">
      <!-- Ejemplo de tabla Listado -->
      <div class="card">
        <div class="card-header">
          <i class="fa fa-align-justify"></i> Grupos Permisos
          <button
            type="button"
            @click="abrirModal('add')"
            class="btn btn-secondary"
          >
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
        </div>
        <div class="card-body">
          <div class="form-group row">
            <div class="col-md-6">
              <div class="input-group">
                <select class="form-control col-md-3" id="opcion" name="opcion">
                  <option value="nombre">Nombre</option>
                </select>
                <input
                  type="text"
                  id="texto"
                  name="texto"
                  class="form-control"
                  placeholder="Texto a buscar"
                />
                <button type="submit" class="btn btn-primary">
                  <i class="fa fa-search"></i> Buscar
                </button>
              </div>
            </div>
          </div>
          <table class="table table-bordered table-striped table-sm">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Opciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in lista" :key="item.id">
                <td v-text="item.name"></td>
                <td>
                  <div v-if="item.status==1">
                    <span class="badge badge-success">Activo</span>
                  </div>
                  <div v-else>
                    <span class="badge badge-danger">Inactivo</span>
                  </div>
                </td>
                <td>
                  <button
                    type="button"
                    @click="abrirModal('edit',item)"
                    class="btn btn-warning btn-sm"
                  >
                    <i class="icon-pencil"></i>
                  </button> &nbsp;
                  <button type="button" class="btn btn-danger btn-sm">
                    <i class="icon-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <nav>
            <ul class="pagination">
              <li class="page-item">
                <a class="page-link" href="#">Ant</a>
              </li>
              <li class="page-item active">
                <a class="page-link" href="#">1</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="#">2</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="#">3</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="#">4</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="#">Sig</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
      <!-- Fin ejemplo de tabla Listado -->
    </div>
    <!--Inicio del modal agregar/actualizar-->
    <div
      class="modal fade"
      tabindex="-1"
      :class="{'mostrar' : modal}"
      role="dialog"
      aria-labelledby="myModalLabel"
      style="display: none;"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-primary modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 v-text="tituloModal" class="modal-title"></h4>
            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <form action method="post" enctype="multipart/form-data" class="form-horizontal">
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                <div class="col-md-9">
                  <input
                    type="text"
                    v-model="name"
                    class="form-control"
                    placeholder="Nombre del Grupo"
                  />
                </div>
              </div>
              <div v-show="error" class="form-group row div-error">
                  <dir class="text-center text-error">
                      <div v-for="err in errores" :key="err" v-text="err"></div>
                  </dir>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" @click="cerrarModal()" class="btn btn-secondary" >Cerrar</button>
            <button type="button" v-if="accion == 1" class="btn btn-primary" @click="registrar()">Guardar</button>
            <button type="button" v-if="accion == 2" class="btn btn-primary" @click="actualizar()"> Actualizar</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->
    <!-- Inicio del modal Eliminar -->
    <div
      class="modal fade"
      id="modalEliminar"
      tabindex="-1"
      role="dialog"
      aria-labelledby="myModalLabel"
      style="display: none;"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-danger" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Eliminar Categoría</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Estas seguro de eliminar la categoría?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-danger">Eliminar</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- Fin del modal Eliminar -->
  </main>
</template>

<script>
export default {
  data() {
    return {
        pk:0,
      name: "",
      lista: [],
      modal: 0,
      tituloModal: "",
      accion: 0,
      error: 0,
      errores: []
    };
  },
  methods: {
    listar() {
      let me = this;
      axios
        .get("Grupos_permisos")
        .then(function(response) {
          me.lista = response.data.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    validar(){
        this.error=0;
        this.errores=[];

        if (!this.name){
            this.errores.push('Debe indicar un Nombre');
        }

        if (this.errores.length) {
            this.error=1;
        }
        return this.error;

    },
    registrar() {
        if (this.validar()){
            return;
        }
        let me = this;
         axios
        .post("Grupos_permisos",{
            'name' : this.name
        })
        .then(function(response) {
          me.cerrarModal();
          me.listar();
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    actualizar() {
        if (this.validar()){
            return;
        }
        let me = this;
         axios
        .put("Grupos_permisos/"+this.pk,{
            'id' : this.pk,
            'name' : this.name
        })
        .then(function(response) {
          me.cerrarModal();
          me.listar();
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    cerrarModal() {
        this.modal = 0;
        this.tituloModal = '';
        this.name = '';
        this.error=0;
        this.errores = [];
    },
    abrirModal(accion, data = []) {
    this.error=0;
    this.errores = [];
      switch (accion) {
        case "add": {
          this.modal = 1;
          this.name = "";
          this.tituloModal = "Registrar";
          this.accion = 1;
          break;
        }
        case "edit": {
          this.modal = 1;
          this.name = data.name;
          this.pk = data.id;
          this.tituloModal = "Editar";
          this.accion = 2;
          break;
        }
      }
    }
  },
  mounted() {
    this.listar();
  }
};
</script>
<style >
.modal-content {
  width: 100% !important;
  position: absolute !important;
}
.mostrar {
  display: list-item !important;
  opacity: 1 !important;
  position: absolute !important;
  background-color: #3c29297a !important;
}
.div-error{
    display: flex;
    justify-content: center;
}
.text-error{
    color: red !important;
    font-weight: bold;
}
</style>
