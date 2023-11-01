<?php



class Controlador
{
    public  function cargarArchivo()
    {
        $rutaActual = getcwd();  // Obtener la ruta absoluta del directorio actual
        $target_dir = $rutaActual . "/temp_file/"; // Ruta absoluta para la carpeta de temp_file

        // Verifica si el directorio no existe
        if (!file_exists($target_dir)) {
            // Crea el directorio y establece permisos 755
            mkdir($target_dir, 0755);
        }

        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $fileNameWithoutExt = pathinfo($target_file, PATHINFO_FILENAME);

        $estados = Controlador::obtenerClavesEstados();
        // Comprueba si el archivo ya existe
        if (file_exists($target_file)) {

            return array(
                'status' => true,
                'mensaje' => "El archivo " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " ha sido cargado.",
                'ruta' => $target_file,
                'nombre_archivo' => $fileNameWithoutExt,
                'estado' => Controlador::obtenerCalveEstadoDeCurp($fileNameWithoutExt)
            );
        }

        // Comprueba el tamaño del archivo (por ejemplo, limita a 5MB)
        if ($_FILES["fileToUpload"]["size"] > 5000000) {
            return array(
                'status' => false,
                'mensaje' => "El archivo es demasiado grande.",
                'ruta' => '',
                'nombre_archivo' => $fileNameWithoutExt,
            );
        }

        // Permite solo ciertos formatos de archivo (en este caso, solo PDF)
        if ($fileType != "pdf") {
            return array(
                'status' => false,
                'mensaje' => "Solo se permiten archivos PDF.",
                'ruta' => '',
                'nombre_archivo' => $fileNameWithoutExt
            );
        }

        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            return array(
                'status' => true,
                'mensaje' => "El archivo " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " ha sido cargado.",
                'ruta' => $target_file,
                'nombre_archivo' => $fileNameWithoutExt,
                'estado' => Controlador::obtenerCalveEstadoDeCurp($fileNameWithoutExt)
            );
        } else {
            return array(
                'status' => false,
                'mensaje' => "Hubo un error al cargar el archivo.",
                'ruta' => '',
                'nombre_archivo' => $fileNameWithoutExt
            );
        }
    }
    public  function eliminarArchivo($rutaCompleta)
    {
        if (file_exists($rutaCompleta)) {
            // Intenta eliminar el archivo
            if (unlink($rutaCompleta)) {
                return array(
                    'status' => true,
                    'mensaje' => "El archivo ha sido eliminado exitosamente."
                );
            } else {
                return array(
                    'status' => false,
                    'mensaje' => "Hubo un error al eliminar el archivo."
                );
            }
        } else {
            return array(
                'status' => false,
                'mensaje' => "El archivo especificado no existe."
            );
        }
    }

    public static function obtenerEstadoDeCurp($curp, $estados)
    {
        $curpExtraida = substr($curp, 0, 18);
        // Extraemos las letras correspondientes al estado (posiciones 12 y 13)
        $claveEstado = substr($curpExtraida, 11, 2);

        // Buscamos el estado en el arreglo
        if (isset($estados[$claveEstado])) {
            return $estados[$claveEstado];
        } else {
            return "Clave de estado no reconocida";
        }
    }

    public static function obtenerCalveEstadoDeCurp($curp)
    {
        $curpExtraida = substr($curp, 0, 18);
        // Extraemos las letras correspondientes al estado (posiciones 12 y 13)
        return substr($curpExtraida, 11, 2);
    }

    public static function obtenerClavesEstados()
    {
        return array(
            "AS" => "Aguascalientes",
            "BC" => "Baja California",
            "BS" => "Baja California Sur",
            "CC" => "Campeche",
            "CL" => "Coahuila",
            "CM" => "Colima",
            "CS" => "Chiapas",
            "CH" => "Chihuahua",
            "DF" => "Ciudad de México",
            "DG" => "Durango",
            "GT" => "Guanajuato",
            "GR" => "Guerrero",
            "HG" => "Hidalgo",
            "JC" => "Jalisco",
            "MC" => "México",
            "MN" => "Michoacán",
            "MS" => "Morelos",
            "NT" => "Nayarit",
            "NL" => "Nuevo León",
            "OC" => "Oaxaca",
            "PL" => "Puebla",
            "QT" => "Querétaro",
            "QR" => "Quintana Roo",
            "SP" => "San Luis Potosí",
            "SL" => "Sinaloa",
            "SR" => "Sonora",
            "TC" => "Tabasco",
            "TS" => "Tamaulipas",
            "TL" => "Tlaxcala",
            "VZ" => "Veracruz",
            "YN" => "Yucatán",
            "ZS" => "Zacatecas",
            "NE" => "Nacido en el extranjero"
        );
    }
}

if (isset($_POST['cargarArchivo'])) {
    $cargarArvhivo = new Controlador();
    echo json_encode($cargarArvhivo->cargarArchivo(), true);
}



// // Ejemplo de uso
// $resultado = cargarArchivo();
// print_r($resultado);

// $rutaDelArchivo = $resultado['ruta'];
// $resultado = eliminarArchivo($rutaDelArchivo);
// print_r($resultado);
