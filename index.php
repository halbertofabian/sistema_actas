<?php
require_once 'controlador.php';
?>
<!doctype html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <title>Sistema para generar actas</title>
</head>

<body>

    <div class="container-fluid">
        <div class="row justify-content-center  vh-100"> <!-- Agregado justify-content-center y align-items-center y vh-100 para centrado vertical y horizontal -->
            <div class="col-12 col-md-10">
                <div class="card">
                    <div class="card-body">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-1-tab" data-bs-toggle="tab" data-bs-target="#nav-1" type="button" role="tab" aria-controls="nav-1" aria-selected="true">Generar acta</button>
                                <button class="nav-link" id="nav-2-tab" data-bs-toggle="tab" data-bs-target="#nav-2" type="button" role="tab" aria-controls="nav-2" aria-selected="false">Listar actas</button>
                                <button class="nav-link" id="nav-3-tab" data-bs-toggle="tab" data-bs-target="#nav-3" type="button" role="tab" aria-controls="nav-3" aria-selected="false">Reversos</button>
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
                                                        <input type="file" class="form-control" name="fileToUpload" id="fileToUpload" accept=".pdf, .PDF">
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
                                                <form action="" method="post">
                                                    <div class="form-group">
                                                        <label for="clave_estado">Selecione un estado</label>
                                                        <select name="clave_estado" id="clave_estado" class="form-control">
                                                            <option value=""></option>
                                                            <?php foreach ($array_estados as $key => $estados) : ?>
                                                                <option value="<?= $key ?>"><?= $estados ?></option>
                                                            <?php endforeach; ?>

                                                        </select>
                                                        <small id="helpId" class="text-muted text-center" style="font-size: 8px;">Si el estado no se seleccionó automáticamente, por favor, elígelo manualmente. </small>
                                                    </div>
                                                    <hr>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="" id="sinReverso">
                                                        <label class="form-check-label" for="sinReverso">
                                                            Sin reverso
                                                        </label>
                                                    </div>
                                                    <div>
                                                        <button type="submit" class="btn btn-primary btn-sm float-end" name="submit">Generar</button>
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-2" role="tabpanel" aria-labelledby="nav-2-tab">...</div>
                            <div class="tab-pane fade" id="nav-3" role="tabpanel" aria-labelledby="nav-3-tab">...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>






    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->

    <script>
        $(document).ready(function() {

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
                            $("#clave_estado").val(response.estado);
                        } else {
                            $(".mensajeCarga").addClass('alert-danger');
                            $(".mensajeCarga").removeClass('alert-success');

                            $("#msj_respuesta").html(response.mensaje);
                            $("#clave_estado").val("");

                        }
                        // Aquí puedes manejar la respuesta. Por ejemplo:
                        console.log(response)
                    }
                });
            });
        });
    </script>
</body>

</html>