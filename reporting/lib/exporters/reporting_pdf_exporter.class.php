<?php
class ReportingPdfExporter extends ReportingExporter
{
    function export()
    {
        $template = $this->get_template();      
        
        $data = $this->create_save_data($template->export());
        
        $export = Export :: factory('pdf', $data);
        $export->set_filename($this->get_file_name());
    	$export->send_to_browser(); 
    }
    
    function save()
    {
    	$template = $this->get_template();      
        
        $data = $this->create_save_data($template->export());
        
        $export = Export :: factory('pdf', $data);
        $export->set_filename($this->get_file_name());
    	return $export->render_data(); 
    }
    
    function create_save_data($data)
    {
   		$data = str_replace(Path :: get(WEB_PATH), Path :: get(SYS_PATH), $data);
   		
   		//REMOVE noscript tags
   		$data = str_replace('<noscript>', '', $data);
   		$data = str_replace('</noscript>', '', $data);
   		$data = preg_replace('/<button(.*)>/', '', $data);
   		
   		return $data;
    }
}
?>