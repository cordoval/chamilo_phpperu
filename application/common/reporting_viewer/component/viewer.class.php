<?php
class ReportingViewerViewerComponent extends ReportingViewerComponent
{
	function run()
	{
		$template_registration = $this->get_template();
		$trail = $this->get_breadcrumb_trail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get(Utilities :: underscores_to_camelcase($template_registration->get_template()))));
		$this->display_header($trail);
	
		$template = ReportingTemplate::factory($template_registration, $this);
		echo($template->to_html());
		
		$this->display_footer();
	}
}
?>
