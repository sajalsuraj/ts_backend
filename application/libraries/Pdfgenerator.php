<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('DOMPDF_ENABLE_AUTOLOAD', false);

use Dompdf\Dompdf;

class Pdfgenerator {

  public function generate($html, $filename='', $stream=TRUE, $paper = 'A4', $orientation = "portrait")
  {
    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $dompdf->set_paper($paper, $orientation);
    $dompdf->render();
    // $canvas = $dompdf->get_canvas();
    // $font = Font_Metrics::get_font("helvetica", "normal");
    // if ($stream) {
    //     $dompdf->stream($filename.".pdf", array("Attachment" => 0));
    // } else {
      //$dompdf->stream($filename.".pdf", array("Attachment" => 0));
        return $dompdf->output();
   // }
  }
}