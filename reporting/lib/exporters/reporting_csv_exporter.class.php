<?php
class ReportingCsvExporter extends ReportingExporter
{
	function export()
    {
        $file = $this->get_file_name();
        $export = Export :: factory('csv', $file);

        $data = $this->convert_data();
		$export->write_to_file($data); 
    }
    
    function convert_data()
    {
    	$template = $this->get_template();
    	$block = $template->get_current_block();
        $data = $block->retrieve_data();
        
        $csv_data = array();
        
        foreach($data->get_categories() as $category_id => $category_name)
    	{
    		$category_array = array();
    		if ($data->is_categories_visible())
    		{
    			$category_array[Translation::get('Category')] = $category_name;
    		}
    		foreach ($data->get_rows() as $row_id => $row_name)
    		{
    			$category_array[$row_name] = strip_tags($data->get_data_category_row($category_id, $row_id));
    		}
    		$csv_data[] = $category_array;
    	}
    	return $csv_data;
    }
}
?>