<?php
/**
 * $Id: excel_export.class.php 128 2010-05-21 13:56:20Z David Hosse $
 * @package common.export.excel
 */
require_once dirname(__FILE__) . '/../export.class.php';
require_once dirname(__FILE__) . '/../layout/excel_layout.class.php';
//require_once Path :: get_plugin_path() . 'phpexcel/PHPExcel/Style/Color.php';

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
		//Excel layout
		$excel_layout = new ExcelLayout();
		$excel_layout->set_background_question('B5CAE7');
		$excel_layout->set_background_answer('FF0000FF');

		$background_question = $excel_layout->get_background_question();
		$background_answer = $excel_layout->get_background_answer();

		//layout array
		$styleArray = array(
				'font' => array('bold' => true, 'size' => 12),
		/*	'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,),
		 'borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN,),),
		 'fill' => array('type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
		 'rotation' => 90,
		 'startcolor' => array('argb' => 'FFA0A0A0',),
		 'endcolor' => array('argb' => 'FFFFFFFF',),),
		 */
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => $background_question,),),
		);
			
			
	  
		$survey_data = $this->get_data();
		$letters = array(0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I', 9 => 'J', 10 => 'K', 11 => 'L', 12 => 'M', 13 => 'N', 14 => 'O', 15 => 'P', 16 => 'Q', 17 => 'R', 18 => 'S', 19 => 'T', 20 => 'U', 21 => 'V', 22 => 'W', 23 => 'X', 24 => 'Y', 25 => 'Z');
			
		$i=0;
		$cell_letter = 0;
		$cell_number = 1;
			
		//dump($survey_data);
		$excel->setActiveSheetIndex(0);


		$question_number = 0;
		foreach($survey_data as $question){
				
			$question_number ++;
				
			$title = $question[0];
			$description = $question[1];
			$data = $question[2];
				
			$cell_letter = 0;
			$cell_number = $cell_number + 2;
			$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, $question_number);
			$cell_letter ++;
			$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, strip_tags(html_entity_decode($title)));
			$excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(60);
				

				
			//layout
			$excel->getActiveSheet()->mergeCells("B".$cell_number.":H".$cell_number."");
			$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
			$excel->getActiveSheet()->getStyle("A".$cell_number.":H".$cell_number."")->applyFromArray($styleArray);
				
			++$cell_number;
			$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, trim(html_entity_decode(strip_tags($description))));

			if ($description != ""){
				$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
			}

			++$cell_number;
			//(matrix question) rows
			foreach ($data->get_rows() as $row_id => $row_name)
			{

				$cell_letter++;
				$excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(15);
				//$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
				$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, trim(html_entity_decode(strip_tags($row_name))));

			}
				
			$excel->getActiveSheet()->getStyle("B".$cell_number.":H".$cell_number."")->applyFromArray($styleArray);
					
			foreach($data->get_categories() as $category_id => $category_name)
			{
				$cell_letter = 1;
				++$cell_number;
				$excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(50);
				$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, trim(html_entity_decode(strip_tags($category_name))));


				$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
				foreach ($data->get_rows() as $row_id => $row_name)
				{
					$cell_letter++;
					$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, $data->get_data_category_row($category_id, $row_id));
				}
				$i++;
			}			
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
	function wrap_text($excel, $cell){
		//$excel->getActiveSheet()->getStyle($cell)->getAlignment()->setWidth(20);
		$excel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);

	}

	function get_type()
	{
		return self :: EXPORT_TYPE;
	}
}
?>