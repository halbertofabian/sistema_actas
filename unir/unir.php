<?php

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
            $this->useTemplate($tpl1, 0, -2, null, null,true);  // Ajusta las coordenadas y el tamaño según lo necesites

            // Importa la segunda página
            $tpl2 = $this->importPage(2);
            $this->useTemplate($tpl2, 0, -2, null, null,true);  // Ajusta las coordenadas y el tamaño según lo necesites

           
        }
    }
}

$pdf = new PdfMerger();
$pdf->mergePages('../temp_file/GAVL670825HGRRZS00_N.pdf');
$pdf->Output('../actas_realizadas/GAVL670825HGRRZS00_N.pdf', 'I');  // Guarda el archivo como 'nombre_del_archivo_salida.pdf'
