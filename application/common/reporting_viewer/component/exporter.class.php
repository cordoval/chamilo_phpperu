<?php
class ReportingViewerExporterComponent extends ReportingViewerComponent
{
	function run()
	{
		$template_registration = $this->get_template();
		
		$template = ReportingTemplate::factory($template_registration, $this);
		$export_type = Request :: get(ReportingManager :: PARAM_EXPORT_TYPE);
		$export = ReportingExporter::factory($export_type, $template);
		$export->export();
		/*$filename = $template->get_name() . date('_Y-m-d_H-i-s');
        $export = Export :: factory($export_type, $filename);
        
        switch ($export_type)
        {
            case 'xml' :
                $export->write_to_file($data);
                
                break;
            
            case 'pdf' :
                $data = implode("\n", $html);
                $data = str_replace(Path :: get(WEB_PATH), Path :: get(SYS_PATH), $data);
                $export->write_to_file_html($data);
                break;
            
            case 'csv' :
                $export->write_to_file($data);
                break;
            
            default :
                $export->write_to_file_html($data);
                break;
        }*/
		
		//$this->display_footer();
	}
}
?>