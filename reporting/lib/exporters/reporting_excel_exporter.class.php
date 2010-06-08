<?php
class ReportingExcelExporter extends ReportingExporter
{
	function export()
    {
    	$template = $this->get_template();
    	$block = $template->get_current_block();
        $data = $block->retrieve_data();
        
		$export = Export :: factory('excel', $data);
        $export->set_filename($this->get_file_name());
    	$export->send_to_browser(); 
    }
    
    function save()
    {
    	$template = $this->get_template();
    	$block = $template->get_current_block();
        $data = $block->retrieve_data();
        
    	$file = $this->get_file_name();
        $export = Export :: factory('excel', $data);
        $export->set_filename($this->get_file_name());
    	return $export->render_data(); 
    }
}
?>