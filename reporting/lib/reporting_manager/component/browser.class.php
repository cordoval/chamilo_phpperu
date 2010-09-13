<?php
/**
 * $Id: browser.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_manager.component
 * @author Michael Kyndt
 */

class ReportingManagerBrowserComponent extends ReportingManager implements AdministrationComponent
{
    private $action_bar;
    private $application;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $application = $this->application = Request :: get('app');

        if (! $application)
            $application = $this->application = 'admin';

        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        $this->action_bar = $this->get_action_bar();
        //$output = $this->get_template_html();


        $this->display_header();
        echo '<br />' . $this->action_bar->as_html() . '<br />';
        echo '<div id="applications" class="applications">';
        echo $this->get_applications();
        if (isset($application))
            echo $this->get_template_html();
        else
            echo $this->get_templates();
        unset($application);
        echo '</div>';
        //echo $output;
        $this->display_footer();
    }

    /**
     * Gets all the installed applications
     */
    function get_applications()
    {
        $application = $this->application;

        $html = array();

        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/reporting_menu.js' . '"></script>';
        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/reporting_menu_interface.js' . '"></script>';
        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/reporting_dock.js' . '"></script>';

        $html[] = '<div class="dock" id="dock">';
        $html[] = '<div class="dock-container"> ';
        $applications = WebApplication :: load_all();
        $admin_manager = CoreApplication :: factory('admin', $this->get_user());
        $links = $admin_manager->get_application_platform_admin_links();

        foreach ($links as $application_links)
        {
            if (isset($application) && $application == $application_links['application']['class'])
            {
                //$html[] = '<div class="application_current">';
            }
            else
            {
                //$html[] = '<div class="application">';
            }
            //$html[] = '<a id="'.$application_links['application']['class'].'" class="dock-item" href="'. $this->get_url(array(Application :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES, ReportingManager :: PARAM_APPLICATION => $application_links['application']['class'])) .'">';
            //$html[] = '<a class="dock-item" href="#tabs-'.$index.'" />';
            $html[] = '<a id="' . $application_links['application']['class'] . '" class="dock-item" href="core.php?application=reporting&go=browser&app=' . $application_links['application']['class'] . '" />'; //. '#application-'.$application_links['application']['class']
            $html[] = '<img src="' . Theme :: get_image_path('admin') . 'place_' . $application_links['application']['class'] . '.png" alt="' . $application_links['application']['name'] . '" title="' . $application_links['application']['name'] . '"/>';
            $html[] = '<span>' . $application_links['application']['name'] . '</span>';
            $html[] = '</a>';
        }

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div><br /><br />';
        return implode("\n", $html);
    }

    function get_templates()
    {
        $html = array();

        $html[] = '<div id="applications-list" class="applications-list" >';

        $admin_manager = CoreApplication :: factory('admin', $this->get_user());
        $links = $admin_manager->get_application_platform_admin_links();

        foreach ($links as $application_links)
        {
            $this->application = $application_links['application']['class'];
            $html[] = '<div id="application-' . $this->application . '">';
            $html[] = $this->get_template_html();
            $html[] = '</div>';
            $html[] = '<div class="clear"></div>';
        }
        $html[] = '</div>';

        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/reporting_browser.js' . '"></script>';
        return implode("\n", $html);
    }

    /**
     * Converts an array of templates for this application to a table
     */
    function get_template_html()
    {
        $parameters = $this->get_parameters();
        $parameters[ReportingManager :: PARAM_APPLICATION] = $this->application;
    	$parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
    	
    	$table = new ReportingTemplateRegistrationBrowserTable($this, $parameters, $this->get_condition());
        $html = array();
        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(ReportingTemplateRegistration :: PROPERTY_TITLE, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(ReportingTemplateRegistration :: PROPERTY_APPLICATION, '*' . $query . '*');
            $orcond = new OrCondition($conditions);
            $condition = new EqualityCondition('platform', '1');
            $cond = new AndCondition($orcond, $condition);
        }
        else
        {
            $conditions[] = new EqualityCondition('application', $this->application);
            $conditions[] = new EqualityCondition('platform', '1');
            $cond = new AndCondition($conditions);
        }
        return $cond;
    }

    function get_reporting_template()
    {
        return (Request :: get(ReportingManager :: PARAM_TEMPLATE_ID) ? Request :: get(ReportingManager :: PARAM_TEMPLATE_ID) : 0);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(ReportingManager :: PARAM_TEMPLATE_ID => $this->get_reporting_template())));
        //$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path().'action_add.png', $this->get_url(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_CREATE_ROLE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));


        return $action_bar;
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('reporting_browser');
    }
    
    function get_additional_parameters()
    {
    	return array(ReportingManager :: PARAM_TEMPLATE_ID);
    }
}
?>