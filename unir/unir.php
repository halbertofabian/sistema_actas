<?php
include_once '../config.php';
require_once('vendor/autoload.php');

use setasign\Fpdi\Tcpdf\Fpdi;

class PdfMerger extends Fpdi
{
    public function mergePages($file)
    {

        $pageCount = $this->setSourceFile($file);

        // Por simplicidad, solo combinaré las primeras 2 páginas. Puedes ajustar esto según tus necesidades.
        if ($pageCount >= 2) {

            $this->setFontSubsetting(true);
            $this->setPrintHeader(false);
            $this->setPrintFooter(false);
            $this->SetMargins(0, 0, 0, 0);
            // $this->setPrintHeader(false);
            $this->SetFooterMargin(0);
            $this->SetAutoPageBreak(TRUE, 0);
            $this->AddPage('P');  // Agrega una página en formato paisaje (landscape)

            // Importa la primera página
            $tpl1 = $this->importPage(1);
            $this->useTemplate($tpl1, 0, -2, null, null, true);  // Ajusta las coordenadas y el tamaño según lo necesites

            // Importa la segunda página
            $tpl2 = $this->importPage(2);
            $this->useTemplate($tpl2, 0, -2, null, null, true);  // Ajusta las coordenadas y el tamaño según lo necesites


        }
    }
}

$ruta_archivo = $_POST['ruta'];  // Ruta completa del archivo

$pdf = new PdfMerger();
$pdf->mergePages($ruta_archivo);

// Guarda el archivo combinado en la misma ruta y con el mismo nombre
$pdf->Output($ruta_archivo, 'F');

// Verifica si el archivo se guardó correctamente
if (file_exists($ruta_archivo)) {
    echo json_encode(array('status' => true, 'mensaje' => 'La combinación se realizó con éxito'));
} else {
    echo json_encode(array('status' => false, 'mensaje' => 'Error al realizar la combinación de archivos'));
}
