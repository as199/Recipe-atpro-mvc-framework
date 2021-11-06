<?php

namespace Atpro\mvc\Config\services;

use Dompdf\Dompdf;

/**
 * @author Assane Dione <atpro0290@gmail.com>
 */
class AtproPdf
{   /**
    * @author Assane Dione <atpro0290@gmail.com>
    *
    * @param  $htmlDataFormat
    * @param array $paperFormat
    * @param  $fileName
    * @return void
    */
    public static function pdf($htmlDataFormat, array $paperFormat, $fileName = null)
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($htmlDataFormat);
        /**
         * (Optional) Setup the paper size and orientation
         * $dompdf->setPaper('A4', 'landscape');
         * $dompdf->setPaper('A4', 'portrait');
         */
        foreach ($paperFormat as $key => $value) {
            $dompdf->setPaper($key, $value);
        }

        // Render the HTML as PDF
        $dompdf->render();
        if ($fileName !== null) {
            /**
             * Output the generated PDF to Browser
             */
            $dompdf->stream();
        }
        /**
         * save the pdf file on the server
         */
        $pdf_string =   $dompdf->output();

        file_put_contents($fileName, $pdf_string);
        return $fileName;
    }
}
