<?php
session_start();
E_ALL();

require __DIR__ . "../../vendor/autoload.php";

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

try {
    ob_start();
    // require '../conectar.php';
    require  dirname(__FILE__). '/reporte2.php';
    $content = ob_get_clean();

    $html2pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8');
    $html2pdf->setTestIsImage(true);
    // $html2pdf->_drawImage(false);
    $html2pdf->setTestTdInOnePage(false);
    $html2pdf->AddFont('arial', 'normal', 'arial.php');
    $html2pdf->setDefaultFont('arial');
    // $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content);

    $filename="reporte.pdf";
    $html2pdf->output($filename); 
    exit();
} catch (Html2PdfException $e) {
    $html2pdf->clean();

    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();
}



