<?php
class GradebookManagerViewEvaluationsOnPublicationComponent extends GradebookManager
{
	function run()
	{
		if (!GradebookRights :: is_allowed(GradebookRights :: VIEW_RIGHT, GradebookRights :: LOCATION_BROWSER, 'gradebook_component'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME)), Translation :: get('GradeBook')));
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION=> GradebookManager :: ACTION_BROWSE_GRADEBOOK)), Translation :: get('BrowseEvluations')));

		$this->display_header($trail);
		
		$this->display_footer();
	}
}
?>