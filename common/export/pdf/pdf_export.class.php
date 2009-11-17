<?php
/**
 * $Id: pdf_export.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.export.pdf
 */
require_once dirname(__FILE__) . '/../export.class.php';
/**
 * Exports data to PDF-format
 */
class PdfExport extends Export
{

    public function write_to_file($data)
    {
        require_once Path :: get_plugin_path() . 'ezpdf/class.ezpdf.php';
        $pdf = & new Cezpdf();
        $pdf->selectFont(Path :: get_plugin_path() . 'ezpdf/fonts/Helvetica.afm');
        foreach ($data as $datapair)
        {
            $title = $datapair['key'];
            $table_data = $datapair['data'];
            $pdf->ezTable($table_data, null, $title, array('fontSize' => 5));
        }
        $pdf->ezStream();
    }

    public function write_to_file_html($html)
    {
        require_once Path :: get_plugin_path() . 'html2fpdf/html2fpdf.php';
        
        //$htmlFile = 'http://localhost/chamilo20/run.php?go=courseviewer&course=1&tool=reporting&application=weblcms';
        //$buffer = file_get_contents($htmlFile);
        

        $pdf = new HTML2FPDF('P', 'mm', 'a3');
        $pdf->AddPage();
        $pdf->WriteHTML($html);
        $pdf->Output('test.pdf', 'D');
        
    //        require_once Path :: get_plugin_path().'dompdf/dompdf_config.inc.php';
    //        $theme = Theme :: get_instance();
    //        $dompdf = new DOMPDF();
    //        $dompdf->load_html($html);
    //        //$dompdf->set_base_path($theme->get_path(WEB_CSS_PATH));
    //        //echo $dompdf->get_base_path();
    //        //echo Theme :: get_css_path();
    //        $dompdf->render();
    //        $dompdf->stream("sample.pdf");
    }

}
?>