<?php
/**
 * $Id: view.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_manager.component
 * @author Michael Kyndt
 */

class ReportingManagerViewComponent extends ReportingManagerComponent
{

    //private $template;
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        //$template = $this->template = Request :: get('template');
        $template = Request :: get(ReportingManager :: PARAM_TEMPLATE_ID);
        
        $params = Reporting :: get_params($this);
        
        $reporting_template_registration = $this->retrieve_reporting_template_registration($template);
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => ReportingManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Reporting')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES)), Translation :: get('Reporting')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ReportingManager :: ACTION_VIEW_TEMPLATE, ReportingManager :: PARAM_TEMPLATE_ID => $template)), Translation :: get($reporting_template_registration->get_title())));
        $trail->add_help('reporting general');
        
        $rtv = new ReportingTemplateViewer($this);
        
        $this->display_header($trail);
        $rtv->show_reporting_template($template, $params);
        $this->display_footer();
        //		if (!isset($template))
    //		{
    //			//$template = $this->template = 'reporting';
    //			//error
    //		}
    //
    //        //trail = given trail
    //
    //        //$trail = new BreadcrumbTrail();
    //		//$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES)), Translation :: get('Reporting')));
    //		//$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES, ReportingManager :: PARAM_APPLICATION => $application)), Translation :: get(Application :: application_to_class($application)) . '&nbsp;' . Translation :: get('Template')));
    //
    //		//$trail = new BreadcrumbTrail();
    //		//$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES)), Translation :: get('Reporting')));
    //		//$trail->add(new Breadcrumb($this->get_url(), Translation :: get(Application :: application_to_class($template)) . '&nbsp;' . Translation :: get('Template')));
    //
    //        $rpdm = ReportingDataManager :: get_instance();
    //    	if(!$reporting_template_registration = $rpdm->retrieve_reporting_template_registration($template))
    //        {
    //            $this->display_header($trail);
    //            echo '<a href="javascript:history.go(-1)">' . Translation :: get('Back') . '</a><br /><br />';
    //			Display :: error_message(Translation :: get("NotFound"));
    //			$this->display_footer();
    //			exit;
    //        }
    //
    //        //is platform template
    //        if ($reporting_template_registration->isPlatformTemplate() && !$this->get_user()->is_platform_admin())
    //		{
    //			$this->display_header($trail);
    //            echo '<a href="javascript:history.go(-1)">' . Translation :: get('Back') . '</a><br /><br />';
    //			Display :: error_message(Translation :: get("NotAllowed"));
    //			$this->display_footer();
    //			exit;
    //		}
    //
    //        $application = $reporting_template_registration->get_application();
    //        $base_path = (WebApplication :: is_application($application) ? Path :: get_application_path().'lib/' : Path :: get(SYS_PATH));
    //        $file = $base_path .$application. '/reporting/templates/'.Utilities :: camelcase_to_underscores($reporting_template_registration->get_classname()).'.class.php';;
    //        require_once($file);
    //
    //        $classname = $reporting_template_registration->get_classname();
    //        $template = new $classname($this,$reporting_template_registration->get_id());
    //        //$template->set_registration_id($reporting_template_registration->get_id());
    //
    //        if(Request :: get(ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS))
    //        {
    //            $params = Request :: get(ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS);
    //            $template->set_reporting_blocks_function_parameters($params);
    //        }
    //
    //		$this->display_header($trail,false,false);
    //
    //        echo '<a href="javascript:history.go(-1)">' . Translation :: get('Back') . '</a><br /><br />';
    //
    //        if(Request :: get('s'))
    //		{
    //            $template->show_reporting_block(Request :: get('s'));
    //		}
    //        echo $template->to_html();
    //		$this->display_footer();
    } //run
}
?>