<?php
include_once './config.php';
require_once 'controlador.php';
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
    <title>Login</title>
</head>

<body class="bg-dark">
    <div class="container-fluid">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-body">
                        <form id="formIniciarSesion" class="row g-3 p-3">
                            <h4 class="card-title">¡Bienvenido!</h4>
                            <div class="col-12">
                                <label for="" class="form-label">Correo</label>
                                <input type="email" class="form-control" name="usr_correo" id="" placeholder="example@hotmail.com" required />
                            </div>
                            <div class="col-12">
                                <label for="" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" name="usr_contraseña" id="" required />
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary float-end">
                                    Iniciar sesión
                                </button>
                            </div>
                        </form>
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

    <script>
        $('#formIniciarSesion').on('submit', function(e) {
            e.preventDefault();
            var datos = new FormData(this)
            datos.append('btnIniciarSesion', true);
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
                            location.reload();
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