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
		 
		//dump($data);
		//exit();
		$excel->setActiveSheetIndex(0);
		//$excel->getActiveSheet()->setTitle("test");
		
		if(is_array($data))
		{
			foreach($data as $block_data){
				$block_title = $block_data[0];
				$block_description = $block_data[1];
				$block_content_data = $block_data[2];
				$cell_letter = 0;
				$cell_number = $cell_number + 2;
				$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, strip_tags(html_entity_decode($block_title)));
				$excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(60);
				$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
				++$cell_number;
				$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, trim(html_entity_decode(strip_tags($block_description))));
	
				if ($block_description != ""){
					$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
				}
	
				++$cell_number;
				//(matrix question) rows
				foreach ($block_content_data->get_rows() as $row_id => $row_name)
				{
					//	dump($row_name);
					$cell_letter++;
					$excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(15);
					//$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
					$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, trim(html_entity_decode(strip_tags($row_name))));
	
				}
				foreach($block_content_data->get_categories() as $category_id => $category_name)
				{
					$cell_letter = 0;
					++$cell_number;
					$excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(50);
					$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, trim(html_entity_decode(strip_tags($category_name))));
					$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
					foreach ($block_content_data->get_rows() as $row_id => $row_name)
					{
						$cell_letter++;
						$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, $block_content_data->get_data_category_row($category_id, $row_id));
					}
					$i++;
				}
	
			}
			
		}
		else
		{
			$blocks = $data->get_reporting_blocks();
			
			if(is_array($blocks))
			{
				foreach($blocks as $block)
				{
					$block_title = Utilities::underscores_to_camelcase_with_spaces($block->get_name());
					$block_content_data = $block->retrieve_data();
					
					$cell_letter = 0;
					
					$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, strip_tags(html_entity_decode($block_title)));
					$excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(60);
					
					$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
					++$cell_number;
					$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, trim(html_entity_decode(strip_tags($block_description))));
		
					if ($block_description != ""){
						$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
					}
		
					++$cell_number;
					//(matrix question) rows
					foreach ($block_content_data->get_rows() as $row_id => $row_name)
					{
						$cell_letter++;
						$excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(15);
						//$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
						$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, trim(html_entity_decode(strip_tags($row_name))));
		
					}
					foreach($block_content_data->get_categories() as $category_id => $category_name)
					{
						$cell_letter = 0;
						++$cell_number;
						$excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(50);
						$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, trim(html_entity_decode(strip_tags($category_name))));
						$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
						foreach ($block_content_data->get_rows() as $row_id => $row_name)
						{
							$cell_letter++;
							$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, $block_content_data->get_data_category_row($category_id, $row_id));
						}
						$i++;
					}
					
					$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
					$cell_number = $cell_number + 2;
				}
			}
			else 
			{
				$block = $data->get_current_block();
				
				$block_title = Utilities::underscores_to_camelcase_with_spaces($block->get_name());
				$block_content_data = $block->retrieve_data();
				
				$cell_letter = 0;
				
				$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, strip_tags(html_entity_decode($block_title)));
				$excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(60);
					
				$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
				++$cell_number;
				$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, trim(html_entity_decode(strip_tags($block_description))));
	
				if ($block_description != ""){
					$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
				}
	
				++$cell_number;
				//(matrix question) rows
				foreach ($block_content_data->get_rows() as $row_id => $row_name)
				{
					$cell_letter++;
					$excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(15);
					//$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
					$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, trim(html_entity_decode(strip_tags($row_name))));
	
				}
				foreach($block_content_data->get_categories() as $category_id => $category_name)
				{
					$cell_letter = 0;
					++$cell_number;
					$excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(50);
					$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, trim(html_entity_decode(strip_tags($category_name))));
					$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
					foreach ($block_content_data->get_rows() as $row_id => $row_name)
					{
						$cell_letter++;
						$excel->getActiveSheet()->setCellValue($letters[$cell_letter].$cell_number, $block_content_data->get_data_category_row($category_id, $row_id));
					}
					$i++;
				}
				
				$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
				$cell_number = $cell_number + 2;
			}
			
		}		
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$this->get_filename().'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		return $objWriter->save('php://output');

	
			
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