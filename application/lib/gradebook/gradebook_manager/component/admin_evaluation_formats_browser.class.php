<?php
require_once dirname(__FILE__) . '/../gradebook_manager.class.php';
require_once dirname(__FILE__) . '/../gradebook_manager_component.class.php';
// required table classes
require_once dirname(__FILE__).'/evaluation_formats_browser/evaluation_formats_browser_table.class.php';

class GradebookManagerAdminEvaluationFormatsBrowserComponent extends GradebookManagerComponent
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
        
		$this->display_header($trail);
		$this->get_table_html();
		$this->display_footer();
	}
	
	function get_table_html()
	{
		$parameters = $this->get_parameters();
		$parameters[GradebookManager :: PARAM_ACTION]=  GradebookManager :: ACTION_ADMIN_BROWSE_EVALUATION_FORMATS;
		$table = new EvaluationFormatsBrowserTable($this, $parameters);
		echo $table->as_html();
	}
}
?>