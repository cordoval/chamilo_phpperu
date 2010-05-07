<?php
require_once Path :: get_application_path() . 'lib/gradebook/reporting/templates/publication_evaluations_template.class.php';

class GradebookManagerViewEvaluationsOnPublicationComponent extends GradebookManager
{
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME)), Translation :: get('Gradebook')));
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME, GradebookManager :: PARAM_PUBLICATION_TYPE => Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE))), Translation :: get('BrowsePublicationsOf') . ' ' . Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE)));
        
        $rtv = new ReportingViewer($this);
        $rtv->add_template_by_name('publication_evaluations_template', GradebookManager :: APPLICATION_NAME);
        $rtv->set_breadcrumb_trail($trail);
        $rtv->set_parameter(GradebookManager :: PARAM_PUBLICATION_TYPE, Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE));
        $rtv->set_parameter(GradebookManager :: PARAM_PUBLICATION_ID, Request :: get(GradebookManager :: PARAM_PUBLICATION_ID));
        $rtv->show_all_blocks();
        
        $rtv->run();
	}
}
?>