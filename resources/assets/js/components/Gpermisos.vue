<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
      <li class="breadcrumb-item">Home</li>
      <li class="breadcrumb-item">
        Permisos y  Roles
      </li>
      <li class="breadcrumb-item active">Grupos Permisos</li>
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
                  <template v-if="item.status==1">
                  <button type="button" class="btn btn-danger btn-sm" @click="eliminar(item.id)">
                    <i class="icon-trash"></i>
                  </button>
                  </template>
                </td>
              </tr>
            </tbody>
          </table>
          <nav>
            <ul class="pagination">
              <li class="page-item"  v-if="pag.current_page > 1">
                <a class="page-link" href="#" @click="cambiarPag(pag.current_page - 1)">Ant</a>
              </li>

              <li class="page-item " v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                <a class="page-link" href="#" @click.prevent="cambiarPag(page)" v-text="page" ></a>
              </li>

              <li class="page-item"  v-if="pag.current_page < pag.last_page">
                <a class="page-link" href="#"  @click="cambiarPag(pag.current_page + 1)">Sig</a>
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
      errores: [],
      pag: 0,
      offset: 3
    };
  },
  methods: {
    listar(page) {
      let me = this;
      var url = 'Grupos_permisos?page='+page;
      axios
        .get(url)
        .then(function(response) {
          me.lista = response.data.data;
          delete(response.data.data);
          me.pag=response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    cambiarPag(page){
        let me = this;
        me.pag.current_page = page;
        me.listar(page);
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
        .put("Grupos_permisos/"+this.pk, {
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
    eliminar(id){
const swalWithBootstrapButtons = Swal.mixin({
  confirmButtonClass: 'btn btn-success',
  cancelButtonClass: 'btn btn-danger',
  buttonsStyling: false,
});

swalWithBootstrapButtons({
  title: 'Seguro que desea Eliminar?',
  text: "No podra deshacer!",
  type: 'warning',
  showCancelButton: true,
  confirmButtonText: 'Aceptar',
  cancelButtonText: 'Cancelar',
  reverseButtons: true
}).then((result) => {
  if (result.value) {

 let me = this;
         axios
        .delete("Grupos_permisos/"+id, {
            'id' : id
        })
        .then(function(response) {
            console.log(response);
          me.listar();
            swalWithBootstrapButtons(
            'Borrado!',
            'El registro se elimino.',
            'success'
            );
        })
        .catch(function(error) {
          console.log(error);
        });


  } else if (
    // Read more about handling dismissals
    result.dismiss === Swal.DismissReason.cancel
  ) {
    swalWithBootstrapButtons(
      'Cancelled',
      'Your imaginary file is safe :)',
      'error'
    )
  }
})

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
  computed: {
      isActived: function(){
          return this.pag.current_page;
      },
      pagesNumber: function(){
          if (!this.pag.to){
              return [];
          }
          var from = this.pag.current_page - this.offset;
          if (from < 1){
              from = 1;
          }
          var to = from + (this.offset * 2);
          if (to >= this.pag.last_page){
              to = this.pag.last_page;
          }
          var pagesArray = [];
          while (from <= to){
              pagesArray.push(from);
              from++;
          }
          return pagesArray;

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
