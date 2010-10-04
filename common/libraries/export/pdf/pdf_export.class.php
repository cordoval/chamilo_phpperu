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
	const EXPORT_TYPE = 'pdf';
	
    public function render_data()
    {
    	$data = $this->get_data();
    	if (is_array($data))
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
	        return $pdf->ezOutput();
    	}
    	else
    	{
    		require_once Path :: get_plugin_path() . 'html2pdf/html2pdf.class.php';
        	$pdf = new HTML2PDF('p', 'A4', 'en');
        	$pdf->WriteHTML($pdf->getHtmlFromPage($data));
        	return $pdf->Output('', 'S');
    	}
    }
    
	 function get_type()
	 {
	 	return self :: EXPORT_TYPE;
	 }
}
?>