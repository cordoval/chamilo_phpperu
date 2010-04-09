<?php
class ReportingViewerSaverComponent extends ReportingViewerComponent
{
	function run()
	{
		$trail = new BreadcrumbTrail();
		$this->display_header($trail);
		
		$this->display_footer();
	}
}
?>