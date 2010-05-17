<?php

class GradebookManagerAdminEditEvaluationFormatComponent extends GradebookManager
{
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => GradebookManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Gradebook') ));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseEvaluationFormats')));
	
		if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail, false, true);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
		$id = Request :: get(GradebookManager :: PARAM_EVALUATION_FORMAT);
		if($id)
		{
			$evaluation_format = $this->retrieve_evaluation_format($id);
			$trail->add(new Breadcrumb($this->get_url(), ucfirst($evaluation_format->get_title())));
		}
        
		$this->display_header($trail); 
		$this->display_footer();
	}
}
?>