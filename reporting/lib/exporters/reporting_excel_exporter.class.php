<?php
class ReportingExcelExporter extends ReportingExporter
{
	function export()
    {
    	$template = $this->get_template();      
        
        //$data = $template->export();
        //$export = Export :: factory('excel', $data);
        $export = Export :: factory('excel', $template);
        $export->set_filename($this->get_file_name());
    	$export->send_to_browser(); 
    }
    
    function save()
    {
    	$template = $this->get_template();      
        
        //$data = $template->export();
        //$export = Export :: factory('excel', $data);
        $export = Export :: factory('excel', $template);
        $export->set_filename($this->get_file_name());
    	return $export->render_data(); 
    }
}
?>