<?php if (isset($_SESSION['session']) && $_SESSION['session']) :
?>
    <!doctype html>
    <html lang="es">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <link href="<?= HTTP_HOST ?>app-assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?= HTTP_HOST ?>app-assets/css/fontawesome.min.css" rel="stylesheet">
        <!-- <link href="https://cdn.datatables.net/v/bs5/dt-1.13.6/af-2.6.0/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet"> -->
        <link href="<?= HTTP_HOST ?>app-assets/css/datatables.min.css" rel="stylesheet">
        <link href="<?= HTTP_HOST ?>app-assets/css/select2.min.css" rel="stylesheet">
        <link href="<?= HTTP_HOST ?>app-assets/css/select2-bootstrap-5-theme.min.css" rel="stylesheet">
        <link href="<?= HTTP_HOST ?>app-assets/css/select2-bootstrap-5-theme.rtl.min.css" rel="stylesheet">

        <script src="<?= HTTP_HOST ?>app-assets/js/jquery.js"></script>
        <!-- <script src="https://cdn.datatables.net/v/bs5/dt-1.13.6/af-2.6.0/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.js"></script> -->
        <script src="<?= HTTP_HOST ?>app-assets/js/datatables.min.js"></script>
        <script src="<?= HTTP_HOST ?>app-assets/js/sweetalert.js"></script>
        <title>Sistema para generar actas</title>
    </head>

    <body>
        <nav class="navbar navbar-dark bg-dark mb-3 sticky-top">
            <div class="container-fluid">
                <a class="navbar-brand"><i class="fa fa-file-alt"></i> Sistema de actas</a>
                <div class="dropdown d-flex">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="triggerId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= $_SESSION['session_usr']['usr_correo'] ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="triggerId">
                        <!-- <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item disabled" href="#">Disabled action</a>
                        <h6 class="dropdown-header">Section header</h6>
                        <a class="dropdown-item" href="#">Action</a> -->
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item btnCerrarSesion"><i class="fa fa-power-off"></i> Cerrar sesión</button>
                    </div>
                </div>
            </div>
        </nav>
        <div class="container-fluid">

            <div class="card">
                <div class="card-body">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-1-tab" data-bs-toggle="tab" data-bs-target="#nav-1" type="button" role="tab" aria-controls="nav-1" aria-selected="true">Generar acta</button>
                            <!-- <button class="nav-link" id="nav-2-tab" data-bs-toggle="tab" data-bs-target="#nav-2" type="button" role="tab" aria-controls="nav-2" aria-selected="false">Listar actas</button> -->
                            <button class="nav-link" id="nav-3-tab" data-bs-toggle="tab" data-bs-target="#nav-3" type="button" role="tab" aria-controls="nav-3" aria-selected="false">Reversos</button>
                            <button class="nav-link" id="nav-4-tab" data-bs-toggle="tab" data-bs-target="#nav-4" type="button" role="tab" aria-controls="nav-4" aria-selected="false">Usuarios</button>
                            <button class="nav-link" id="nav-5-tab" data-bs-toggle="tab" data-bs-target="#nav-5" type="button" role="tab" aria-controls="nav-5" aria-selected="false">Servicios y paquetes</button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-1" role="tabpanel" aria-labelledby="nav-1-tab">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                            <form id="formUpload" method="post" enctype="multipart/form-data" class="mt-3 mb-2">
                                                <div class="mb-3">
                                                    <label for="fileToUpload" class="form-label">Seleccione el archivo PDF para cargar:</label>
                                                    <input type="file" class="form-control" name="fileToUpload" id="fileToUpload" accept=".pdf, .PDF" required>
                                                </div>
                                                <!-- <button type="submit" class="btn btn-primary btn-sm float-end" name="submit">Cargar PDF</button> -->
                                            </form>
                                        </div>
                                        <div class="col-12 col-md-12">
                                            <div class="alert  mensajeCarga mt-3" role="alert">
                                                <strong id="msj_respuesta" style="font-size: 12px;"></strong>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-12 divPreConversion">

                                            <?php
                                            $array_estados = Controlador::obtenerClavesEstados();
                                            // print_r($estados);
                                            ?>
                                            <form id="formGenerarActa" method="post">
                                                <div class="form-group">
                                                    <label for="clave_estado">Selecione un estado</label>
                                                    <input type="hidden" id="ruta" name="ruta">
                                                    <select name="clave_estado" id="clave_estado" class="form-control select2">
                                                        <option value="">-Seleccionar-</option>
                                                        <?php foreach ($array_estados as $key => $estados) : ?>
                                                            <option value="<?= $key ?>"><?= $estados ?></option>
                                                        <?php endforeach; ?>

                                                    </select>
                                                    <small id="helpId" class="text-muted text-center" style="font-size: 8px;">Si el estado no se seleccionó automáticamente, por favor, elígelo manualmente. </small>
                                                </div>
                                                <hr>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" name="sinReverso" id="sinReverso">
                                                    <label class="form-check-label" for="sinReverso">
                                                        Sin reverso
                                                    </label>
                                                </div>
                                                <div>
                                                    <button type="submit" class="btn btn-primary float-end" name="submit">Generar</button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 col-12">
                                    <iframe class="iframe_acta" src="" frameborder="0" width="100%" height="500px"></iframe>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-2" role="tabpanel" aria-labelledby="nav-2-tab">
                            <div class="row mt-4">
                                <div class="col-12">
                                    <table class="table table-hover " id="data_table_actas" style="width: 100%;">
                                        <thead class="table-dark text-center">
                                            <tr>
                                                <th>#</th>
                                                <th>CURP</th>
                                                <th>ACCIONES</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-3" role="tabpanel" aria-labelledby="nav-3-tab">
                            <form id="formGuardarReversos" method="post">
                                <div class="row mt-4">
                                    <div class="col-12 col-md-4">
                                        <div class="row">
                                            <div class="col-12 col-md-12">
                                                <div class="mb-3">
                                                    <label for="rvs_ruta" class="form-label">Seleccione el archivo PDF para cargar:</label>
                                                    <input type="file" class="form-control" name="rvs_ruta" id="rvs_ruta" accept=".pdf, .PDF" required>
                                                </div>
                                                <!-- <button type="submit" class="btn btn-primary btn-sm float-end" name="submit">Cargar PDF</button> -->
                                            </div>
                                            <div class="col-12 col-md-12 divPreConversion">
                                                <?php
                                                $array_estados = Controlador::obtenerClavesEstados();
                                                // print_r($estados);
                                                ?>
                                                <div class="form-group">
                                                    <label for="rvs_clave">Selecione un estado</label>
                                                    <select name="rvs_clave" id="rvs_clave" class="form-control select2" required>
                                                        <option value="">-Seleccionar-</option>
                                                        <?php foreach ($array_estados as $key => $estados) : ?>
                                                            <option value="<?= $key ?>"><?= $estados ?></option>
                                                        <?php endforeach; ?>

                                                    </select>
                                                </div>
                                                <hr>
                                                <div>
                                                    <button type="submit" class="btn btn-primary float-end" name="submit">Guardar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-12">

                                        <div class="col-12">
                                            <table class="table table-hover " id="data_table_reversos" style="width: 100%;">
                                                <thead class="table-dark text-center">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>ESTADO</th>
                                                        <th>ACCIONES</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="nav-4" role="tabpanel" aria-labelledby="nav-4-tab">
                            <div class="row">
                                <form id="formGuardarUsuario" class="col-md-4">
                                    <div class="mb-3">
                                        <label for="usr_correo" class="form-label">Correo</label>
                                        <input type="hidden" name="usr_id" id="usr_id">
                                        <input type="email" class="form-control" name="usr_correo" id="usr_correo" placeholder="example@hotmail.com" required />
                                    </div>
                                    <div class="mb-3">
                                        <label for="usr_contraseña" class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" name="usr_contraseña" id="usr_contraseña" required />
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary float-end">
                                            Guardar
                                        </button>
                                    </div>
                                </form>
                                <div class="col-md-8">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="data_table_usuarios" style="width: 100%;">
                                            <thead class="table-dark text-center">
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">CORREO</th>
                                                    <th scope="col">ACCIONES</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-5" role="tabpanel" aria-labelledby="nav-5-tab">
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="card-title">Servicios</h4>
                                            <form id="formNewServicios" class="row g-3">
                                                <div class="col-12">
                                                    <label for="srv_nombre" class="form-label">Nombre del servicio</label>
                                                    <input type="hidden" name="srv_id" id="srv_id">
                                                    <input type="text" class="form-control text-uppercase" name="srv_nombre" id="srv_nombre" placeholder="" required />
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" id="btnGuardarSrv" class="btn btn-primary float-end">
                                                        Guardar
                                                    </button>
                                                </div>
                                            </form>
                                            <div class="row">
                                                <div class="col-12 mt-3">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover" id="data_table_servicios" style="width: 100%;">
                                                            <thead class="table-dark text-center">
                                                                <tr>
                                                                    <th scope="col">#</th>
                                                                    <th scope="col">NOMBRE</th>
                                                                    <th scope="col">ACCIONES</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="card-title">Paquetes</h4>
                                            <form id="formNewPaquetes" class="row g-3">
                                                <div class="col-12">
                                                    <label for="pqt_nombre" class="form-label">Nombre del paquete</label>
                                                    <input type="text" class="form-control text-uppercase" name="pqt_nombre" id="pqt_nombre" placeholder="" required />
                                                </div>
                                                <div class="col-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover" id="data_table_servicios2" style="width: 100%;">
                                                            <thead class="table-dark text-center">
                                                                <tr>
                                                                    <th scope="col">#</th>
                                                                    <th scope="col">SERVICIO</th>
                                                                    <th scope="col">PRECIO</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-primary float-end">
                                                        Guardar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="card-title">Lista de precios</h4>
                                            <form id="formActualizarPrecios" class="row g-3">
                                                <div class="col-12">
                                                    <label for="pqt_id" class="form-label">Paquetes</label>
                                                    <div class="input-group">
                                                        <select class="form-select select2" name="pqt_id" id="pqt_id">
                                                            <option selected value="">-Seleccionar-</option>
                                                        </select>
                                                        <button class="btn btn-warning btnEditarPaquete d-none" type="button"><i class="fa fa-edit"></i></button>
                                                        <button class="btn btn-danger btnEliminarPaquete d-none" type="button"><i class="fa fa-trash-alt"></i></button>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover" id="data_table_precios" style="width: 100%;">
                                                            <thead class="table-dark text-center">
                                                                <tr>
                                                                    <th scope="col">#</th>
                                                                    <th scope="col">SERVICIO</th>
                                                                    <th scope="col">PRECIO</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-12 d-none btnActualizarPqt">
                                                    <button type="submit" class="btn btn-primary float-end">
                                                        Guardar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Optional JavaScript; choose one of the two! -->

        <!-- Option 1: Bootstrap Bundle with Popper -->
        <script src="<?= HTTP_HOST ?>app-assets/js/bootstrap.min.js"></script>
        <script src="<?= HTTP_HOST ?>app-assets/js/fontawesome.min.js"></script>
        <script src="<?= HTTP_HOST ?>app-assets/js/select2.min.js"></script>
        <script src="<?= HTTP_HOST ?>app-assets/js/jquery.number.js"></script>


        <!-- Option 2: Separate Popper and Bootstrap JS -->
        <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->

        <script>
            $(document).ready(function() {
                $('.select2').each(function() {
                    $(this).select2({
                        theme: 'bootstrap-5',
                        dropdownParent: $(this).parent(),
                    });
                });

                mostrarActas();
                mostrarReversos();
                mostrarUsuarios();
                mostrarServicios();
                mostrarServicios2();
                mostrarPaquetes();

                setTimeout(() => {
                    $(".inputN").number(true, 2);
                }, 500);

                $("#fileToUpload").on("change", function() {
                    $("#formUpload").submit(); // Envía el formulario cuando se selecciona un archivo
                });
                $("#formUpload").on("submit", function(e) {
                    e.preventDefault(); // Previene el comportamiento por defecto del formulario

                    var formData = new FormData(this);
                    formData.append('cargarArchivo', true)

                    $.ajax({
                        url: "controlador.php",
                        type: "POST",
                        data: formData,
                        processData: false, // Indica a jQuery que no procese los datos
                        contentType: false, // Indica a jQuery que no establezca el tipo de contenido
                        dataType: "json",
                        success: function(response) {

                            if (response.status) {
                                $(".mensajeCarga").addClass('alert-success');
                                $(".mensajeCarga").removeClass('alert-danger');
                                $("#msj_respuesta").html(response.mensaje);
                                // $("#clave_estado").val();
                                $("#clave_estado").val(response.estado).trigger("change");
                                $("#ruta").val(response.ruta);
                            } else {
                                $(".mensajeCarga").addClass('alert-danger');
                                $(".mensajeCarga").removeClass('alert-success');

                                $("#msj_respuesta").html(response.mensaje);
                                // $("#clave_estado").val("");
                                $("#clave_estado").val("").trigger("change");
                                $("#ruta").val("");

                            }
                            // Aquí puedes manejar la respuesta. Por ejemplo:
                            console.log(response)
                        }
                    });
                });
            });

            $('#formGenerarActa').on('submit', function(e) {
                e.preventDefault();
                var fileToUpload = $("#fileToUpload").val();
                if (fileToUpload == "") {
                    return swal("Error", 'Selecciona un archivo', "error");
                }
                var datos = new FormData(this)
                datos.append('btnGenerarActa', true);
                $.ajax({
                    type: 'POST',
                    url: 'controlador.php',
                    data: datos,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            swal({
                                title: '¡Bien!',
                                text: res.mensaje,
                                type: 'success',
                                icon: 'success'
                            }).then(function() {
                                $('#formGenerarActa')[0].reset();
                                $("#fileToUpload").val("")
                                $(".mensajeCarga").removeClass('alert-success');
                                $(".mensajeCarga").removeClass('alert-danger');
                                $("#msj_respuesta").html("");
                                $('.iframe_acta').attr('src', res.ruta_acta);
                                mostrarActas();
                            });
                        } else {
                            swal({
                                title: 'Error!',
                                text: res.mensaje,
                                type: 'error',
                                icon: 'error'
                            }).then(function() {
                                location.reload();
                            });
                        }
                    }
                });
            });

            $('#formGuardarReversos').on('submit', function(e) {
                e.preventDefault();
                var datos = new FormData(this)
                datos.append('btnGuardarReversos', true);
                $.ajax({
                    type: 'POST',
                    url: 'controlador.php',
                    data: datos,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            swal({
                                title: '¡Bien!',
                                text: res.mensaje,
                                type: 'success',
                                icon: 'success'
                            }).then(function() {
                                // location.reload();
                                mostrarReversos();
                                $("#rvs_ruta").val("");
                                $("#rvs_clave").val("").trigger("change");
                            });
                        } else {
                            swal("Oops", res.mensaje, "error");
                        }
                    }
                });
            });

            $('#sinReverso').on('change', function() {
                var clave_estado = $("#clave_estado").val();
                if (clave_estado == "") {
                    $(this).prop('checked', false);
                    return swal("Oops", "Selecciona un estado", "error");
                }

            });

            $(document).on('click', '.btnEliminarActa', function() {
                var ar_id = $(this).attr('ar_id');
                swal({
                    title: '¿Esta seguro de eliminar el acta?',
                    text: 'Esta accion no es reversible',
                    icon: 'warning',
                    buttons: ['No', 'Si, eliminar acta'],
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var datos = new FormData()
                        datos.append('ar_id', ar_id);
                        datos.append('btnEliminarActa', true);
                        $.ajax({
                            type: 'POST',
                            url: 'controlador.php',
                            data: datos,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            success: function(res) {
                                if (res) {
                                    swal({
                                        title: '¡Bien!',
                                        text: res.mensaje,
                                        type: 'success',
                                        icon: 'success'
                                    }).then(function() {
                                        mostrarActas();
                                    });
                                } else {
                                    swal('Oops', res.mensaje, 'error');
                                }
                            }
                        });
                    } else {}
                });
            });
            $(document).on('click', '.btnEliminarReverso', function() {
                var rvs_id = $(this).attr('rvs_id');
                swal({
                    title: '¿Esta seguro de eliminar el reverso?',
                    text: 'Esta accion no es reversible',
                    icon: 'warning',
                    buttons: ['No', 'Si, eliminar reverso'],
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var datos = new FormData()
                        datos.append('rvs_id', rvs_id);
                        datos.append('btnEliminarReverso', true);
                        $.ajax({
                            type: 'POST',
                            url: 'controlador.php',
                            data: datos,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            success: function(res) {
                                if (res) {
                                    swal({
                                        title: '¡Bien!',
                                        text: res.mensaje,
                                        type: 'success',
                                        icon: 'success'
                                    }).then(function() {
                                        mostrarReversos();
                                    });
                                } else {
                                    swal('Oops', res.mensaje, 'error');
                                }
                            }
                        });
                    } else {}
                });
            });

            function mostrarActas() {
                data_table_actas = $('#data_table_actas').DataTable({
                    responsive: true,
                    'ajax': {
                        'url': 'controlador.php',
                        'method': 'POST', //usamos el metodo POST
                        'data': {
                            btnMostrarActas: true,
                        }, //enviamos opcion 4 para que haga un SELECT
                        'dataSrc': ''
                    },
                    'bDestroy': true,
                    order: false,
                    'columns': [{
                            'data': 'ar_id',

                        },
                        {
                            'data': 'ar_curp'
                        },
                        {
                            'data': 'ar_acciones'
                        },
                    ]
                });
            }

            function mostrarReversos() {
                data_table_reversos = $('#data_table_reversos').DataTable({
                    responsive: true,
                    'ajax': {
                        'url': 'controlador.php',
                        'method': 'POST', //usamos el metodo POST
                        'data': {
                            btnMostrarReversos: true,
                        }, //enviamos opcion 4 para que haga un SELECT
                        'dataSrc': ''
                    },
                    'bDestroy': true,
                    order: false,
                    'columns': [{
                            'data': 'rvs_id',

                        },
                        {
                            'data': 'rvs_estado'
                        },
                        {
                            'data': 'rvs_acciones'
                        },
                    ]
                });

            }

            function mostrarUsuarios() {
                data_table_usuarios = $('#data_table_usuarios').DataTable({
                    responsive: true,
                    'ajax': {
                        'url': 'controlador.php',
                        'method': 'POST', //usamos el metodo POST
                        'data': {
                            btnMostrarUsuarios: true,
                        }, //enviamos opcion 4 para que haga un SELECT
                        'dataSrc': ''
                    },
                    'bDestroy': true,
                    order: false,
                    'columns': [{
                            'data': 'usr_id',

                        },
                        {
                            'data': 'usr_correo'
                        },
                        {
                            'data': 'usr_acciones'
                        },
                    ]
                });
            }

            $('#formGuardarUsuario').on('submit', function(e) {
                e.preventDefault();
                var datos = new FormData(this)
                datos.append('btnGuardarUsuario', true);
                $.ajax({
                    type: 'POST',
                    url: 'controlador.php',
                    data: datos,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            swal({
                                title: '¡Bien!',
                                text: res.mensaje,
                                type: 'success',
                                icon: 'success'
                            }).then(function() {
                                $('#formGuardarUsuario')[0].reset();
                                $("#usr_id").val("");
                                mostrarUsuarios();
                            });
                        } else {
                            swal('Oops', res.mensaje, 'error');
                        }
                    }
                });
            });

            $(document).on('click', '.btnEditarUsuario', function() {
                var usr_id = $(this).attr('usr_id');
                var usr_correo = $(this).attr('usr_correo');

                $("#usr_id").val(usr_id);
                $("#usr_correo").val(usr_correo);
            });
            $(document).on('click', '.btnEliminarUsuario', function() {
                var usr_id = $(this).attr('usr_id');
                swal({
                    title: '¿Esta seguro de eliminar este usuario?',
                    text: 'Esta accion no es reversible',
                    icon: 'warning',
                    buttons: ['No', 'Si, eliminar usuario'],
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var datos = new FormData()
                        datos.append('usr_id', usr_id);
                        datos.append('btnEliminarUsuario', true);
                        $.ajax({
                            type: 'POST',
                            url: 'controlador.php',
                            data: datos,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            success: function(res) {
                                if (res.status) {
                                    swal({
                                        title: '¡Bien!',
                                        text: res.mensaje,
                                        type: 'success',
                                        icon: 'success'
                                    }).then(function() {
                                        mostrarUsuarios();
                                    });
                                } else {
                                    swal('Oops', res.mensaje, 'error');
                                }
                            }
                        });
                    } else {}
                });

            });

            $(document).on('click', '.btnCerrarSesion', function() {
                var datos = new FormData()
                datos.append('btnCerrarSesion', true);
                $.ajax({
                    type: 'POST',
                    url: 'controlador.php',
                    data: datos,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            location.reload();
                        }
                    }
                });
            });

            $("#nav-4-tab").click(function() {
                $('#formGuardarUsuario')[0].reset();
                $("#usr_id").val("");
            });
            $("#nav-5-tab").click(function() {
                $("#srv_nombre").val("");
                $("#srv_id").val("");
                $("#btnGuardarSrv").text('Guardar');
            });

            function mostrarServicios() {
                data_table_usuarios = $('#data_table_servicios').DataTable({
                    responsive: true,
                    'ajax': {
                        'url': 'controlador.php',
                        'method': 'POST', //usamos el metodo POST
                        'data': {
                            btnMostrarServicios: true,
                        }, //enviamos opcion 4 para que haga un SELECT
                        'dataSrc': ''
                    },
                    'bDestroy': true,
                    order: false,
                    'columns': [{
                            'data': 'srv_id',

                        },
                        {
                            'data': 'srv_nombre'
                        },
                        {
                            'data': 'srv_acciones'
                        },
                    ]
                });
            }
            $('#formNewServicios').on('submit', function(e) {
                e.preventDefault();
                var datos = new FormData(this)
                datos.append('btnGuardarServicio', true);
                $.ajax({
                    type: 'POST',
                    url: 'controlador.php',
                    data: datos,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            swal({
                                title: '¡Bien!',
                                text: res.mensaje,
                                type: 'success',
                                icon: 'success'
                            }).then(function() {
                                $("#srv_nombre").val("");
                                $("#srv_id").val("");
                                $("#btnGuardarSrv").text('Guardar');
                                mostrarServicios();
                                mostrarServicios2();
                                setTimeout(() => {
                                    $(".inputN").number(true, 2);
                                }, 500);
                            });
                        } else {
                            swal('Oops', res.mensaje, 'error');
                        }
                    }
                });
            });

            $(document).on('click', '.btnEditarServicio', function() {
                var srv_id = $(this).attr('srv_id');
                var srv_nombre = $(this).attr('srv_nombre');
                $("#srv_id").val(srv_id);
                $("#srv_nombre").val(srv_nombre);
                $("#btnGuardarSrv").text('Actualizar');
            });

            $(document).on('click', '.btnEliminarServicio', function() {
                var srv_id = $(this).attr('srv_id');
                swal({
                    title: '¿Esta seguro de eliminar este servicio?',
                    text: 'Esta accion no es reversible',
                    icon: 'warning',
                    buttons: ['No', 'Si, eliminar'],
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var datos = new FormData();
                        datos.append('srv_id', srv_id);
                        datos.append('btnEliminarServicio', true);
                        $.ajax({
                            type: 'POST',
                            url: 'controlador.php',
                            data: datos,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            success: function(res) {
                                if (res.status) {
                                    swal({
                                        title: '¡Bien!',
                                        text: res.mensaje,
                                        type: 'success',
                                        icon: 'success'
                                    }).then(function() {
                                        mostrarServicios();
                                        mostrarServicios2();
                                        mostrarPaquetes();
                                        mostrarPrecios();
                                        setTimeout(() => {
                                            $('#pqt_id').change();
                                            $(".inputN").number(true, 2);
                                        }, 500);
                                    });
                                }else{
                                    swal('Oops', res.mensaje, 'error');
                                }
                            }
                        });
                    } else {}
                });

            });

            function mostrarServicios2() {
                data_table_usuarios = $('#data_table_servicios2').DataTable({
                    responsive: true,
                    'ajax': {
                        'url': 'controlador.php',
                        'method': 'POST', //usamos el metodo POST
                        'data': {
                            btnMostrarServicios2: true,
                        }, //enviamos opcion 4 para que haga un SELECT
                        'dataSrc': ''
                    },
                    'bDestroy': true,
                    order: false,
                    'columns': [{
                            'data': 'srv_id',

                        },
                        {
                            'data': 'srv_nombre'
                        },
                        {
                            'data': 'srv_precio'
                        },
                    ]
                });
            }
            $('#formNewPaquetes').on('submit', function(e) {
                e.preventDefault();
                var datos = new FormData(this)
                datos.append('btnGuardarPaquetes', true);
                $.ajax({
                    type: 'POST',
                    url: 'controlador.php',
                    data: datos,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            swal({
                                title: '¡Bien!',
                                text: res.mensaje,
                                type: 'success',
                                icon: 'success'
                            }).then(function() {
                                $("#pqt_nombre").val("");
                                mostrarPaquetes();
                                mostrarServicios2();
                                setTimeout(() => {
                                    $("#pqt_id").val(res.pqt_id).trigger('change');

                                    $(".inputN").number(true, 2);
                                }, 500);
                            });
                        } else {
                            swal('Oops', res.mensaje, 'error');
                        }
                    }
                });
            });

            function mostrarPaquetes() {
                var datos = new FormData()
                datos.append('btnMostrarPaquetes', true);
                $.ajax({
                    type: 'POST',
                    url: 'controlador.php',
                    data: datos,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $("#pqt_id").html('<option value="" selected>-Seleccionar-</option>');
                        res.forEach(pqt => {
                            $("#pqt_id").append(`<option value="${pqt.pqt_id}">${pqt.pqt_nombre}</option>`);
                        });
                    }
                });
            }

            function mostrarPrecios() {
                data_table_precios = $('#data_table_precios').DataTable({
                    responsive: true,
                    'ajax': {
                        'url': 'controlador.php',
                        'method': 'POST', //usamos el metodo POST
                        'data': {
                            btnMostrarPrecios: true,
                            pqt_id: $('#pqt_id').val(),
                        }, //enviamos opcion 4 para que haga un SELECT
                        'dataSrc': ''
                    },
                    'bDestroy': true,
                    'ordering': false,
                    order: false,
                    'columns': [{
                            'data': 'prc_id'
                        },
                        {
                            'data': 'srv_nombre'
                        },
                        {
                            'data': 'prc_precio'
                        },
                    ]
                });
            }
            $('#pqt_id').on('change', function() {
                $(".btnActualizarPqt").addClass('d-none')
                var pqt_id = $(this).val();
                if (pqt_id != "") {
                    $('.btnEditarPaquete').removeClass('d-none');
                    $('.btnEliminarPaquete').removeClass('d-none');
                } else {
                    $('.btnEditarPaquete').addClass('d-none');
                    $('.btnEliminarPaquete').addClass('d-none');
                }
                mostrarPrecios();
            });
            $(document).on('click', '.btnEditarPaquete', function() {
                $('.inputPqt').attr('readonly', false);
                $(".btnActualizarPqt").removeClass('d-none');
                $(".inputN").number(true, 2);
            });
            $(document).on('click', '.btnEliminarPaquete', function() {
                $('.inputPqt').attr('readonly', true);
                $(".btnActualizarPqt").addClass('d-none');
                swal({
                    title: '¿Esta seguro de eliminar este paquete?',
                    text: 'Esta accion no es reversible',
                    icon: 'warning',
                    buttons: ['No', 'Si, eliminar'],
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var pqt_id = $("#pqt_id").val();
                        var datos = new FormData()
                        datos.append('pqt_id', pqt_id);
                        datos.append('btnEliminarPaquete', true);
                        $.ajax({
                            type: 'POST',
                            url: 'controlador.php',
                            data: datos,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            success: function(res) {
                                if (res.status) {
                                    swal({
                                        title: '¡Bien!',
                                        text: res.mensaje,
                                        type: 'success',
                                        icon: 'success'
                                    }).then(function() {
                                        mostrarPaquetes();
                                        mostrarPrecios();
                                        setTimeout(() => {
                                            $('#pqt_id').change();
                                        }, 500);
                                    });
                                } else {
                                    swal('Oops', res.mensaje, 'error');
                                }
                            }
                        });
                    } else {}
                });
            });

            $('#formActualizarPrecios').on('submit', function(e) {
                e.preventDefault();
                var datos = new FormData(this)
                datos.append('btnActualizarPrecios', true);
                $.ajax({
                    type: 'POST',
                    url: 'controlador.php',
                    data: datos,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            swal({
                                title: '¡Bien!',
                                text: res.mensaje,
                                type: 'success',
                                icon: 'success'
                            }).then(function() {
                                var pqt_id = $("#pqt_id").val();
                                $("#pqt_id").val(pqt_id).trigger('change');
                                mostrarPrecios();
                                setTimeout(() => {
                                    $(".inputN").number(true, 2);
                                }, 500);
                            });
                        } else {
                            swal('Oops', res.mensaje, 'error');
                        }
                    }
                });
            });
        </script>
    </body>

    </html>
<?php else :
    include_once './login.php';
?>

<?php endif; ?>