<?php
/**
 * $Id: excel_export.class.php 128 2010-05-21 13:56:20Z David Hosse $
 * @package common.export.excel
 */
require_once dirname(__FILE__) . '/../export.class.php';
/**
 * Exports data to Excel
 */
class ExcelExport extends Export
{
	const EXPORT_TYPE = 'xlsx';
	
    public function render_data()
    {
    	require_once Path :: get_plugin_path() . 'phpexcel/PHPExcel.php';
    	$excel = new PHPExcel();
    	
    	$data = $this->get_data();
    	$letters = array(0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I', 9 => 'J', 10 => 'K', 11 => 'L', 12 => 'M', 13 => 'N', 14 => 'O', 15 => 'P', 16 => 'Q', 17 => 'R', 18 => 'S', 19 => 'T', 20 => 'U', 21 => 'V', 22 => 'W', 23 => 'X', 24 => 'Y', 25 => 'Z');
		   
	    $i=0;
	    $cell_letter = 0;
	    $cell_number = 1;
	    
	    $excel->setActiveSheetIndex(0); 
	 	foreach($data->get_categories() as $category_id => $category_name)
	    {
	    	$cell_letter = 0;
	    	++$cell_number;
	    	$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, $category_name); 
	    	foreach ($data->get_rows() as $row_id => $row_name)
    		{
    			
    			$cell_letter++;
    			$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, $data->get_data_category_row($category_id, $row_id));
    		}
	    	$i++;
	    }   	
	    	
	 	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$this->get_filename().'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
    	return $objWriter->save('php://output');
		
	    /*$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
    	return $objWriter->save($this->get_filename());*/
    	
    	$excel->disconnectWorksheets();
		unset($excel);
		
	}
	
	 function get_type()
	 {
	 	return self :: EXPORT_TYPE;
	 }
}
?>