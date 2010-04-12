<?php
class ReportingPdfExporter extends ReportingExporter
{
    function export()
    {
        $template = $this->get_template();      
        $data = str_replace(Path :: get(WEB_PATH), Path :: get(SYS_PATH), $template->export());
        $export = Export :: factory('pdf', $data);
        $export->set_filename($this->get_file_name());
    	$export->send_to_browser(); 
    }
    
    function save()
    {
    	$template = $this->get_template();      
        $data = str_replace(Path :: get(WEB_PATH), Path :: get(SYS_PATH), $template->export());
        $export = Export :: factory('pdf', $data);
        $export->set_filename($this->get_file_name());
    	return $export->render_data(); 
    }
}
?>