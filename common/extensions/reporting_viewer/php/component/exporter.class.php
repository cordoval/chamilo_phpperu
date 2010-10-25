<?php
namespace common\extensions\reporting_viewer;
class ReportingViewerExporterComponent extends ReportingViewer
{
	function run()
	{
		$template_registration = $this->get_template();
		
		$template = ReportingTemplate::factory($template_registration, $this);
		$export_type = Request :: get(ReportingManager :: PARAM_EXPORT_TYPE);
		$export = ReportingExporter::factory($export_type, $template);
		$export->export();
	}
}
?>