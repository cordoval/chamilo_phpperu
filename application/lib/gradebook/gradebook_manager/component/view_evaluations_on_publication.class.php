<?php
require_once Path :: get_application_path() . 'lib/gradebook/reporting/templates/publication_evaluations_template.class.php';

class GradebookManagerViewEvaluationsOnPublicationComponent extends GradebookManager
{
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME)), Translation :: get('GradeBook')));
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME, GradebookManager :: PARAM_PUBLICATION_TYPE => Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE))), Translation :: get('BrowsePublicationsOf') . ' ' . Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE)));
        
        $rtv = new ReportingViewer($this);
        $rtv->add_template_by_name('publication_evaluations_template', GradebookManager :: APPLICATION_NAME);
        $rtv->set_breadcrumb_trail($trail);
        $rtv->show_all_blocks();
        
        $rtv->run();
        
//		$this->display_header($trail);
//		$this->display_publication_results(Request :: get(GradebookManager :: PARAM_PUBLICATION_ID));
//		$this->display_footer();
	}
	
//	function display_publication_results($publication_id)
//	{
//        $url = $this->get_url(array(GradebookManager :: PARAM_PUBLICATION_ID => $publication_id));
//        $export_url = $this->get_export_publication_url($publication_id);
//        
//        $parameters = array(GradebookManager :: PARAM_PUBLICATION_ID => $pid, 'url' => $url, 'results_export_url' => $results_export_url);
//        $template = new PublicationEvaluationsTemplate($this);
//        $template->set_parameters($parameters);
//        //$template->set_reporting_blocks_function_parameters($parameters);
//        return $template->to_html();
//	}
}
?>