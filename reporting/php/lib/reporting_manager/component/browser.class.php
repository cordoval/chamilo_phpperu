<?php

namespace reporting;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\BasicApplication;
use common\libraries\CoreApplication;
use common\libraries\WebApplication;
use common\libraries\Request;
use common\libraries\Theme;
use common\libraries\Display;
use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\OrCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\AdministrationComponent;
use common\libraries\BreadcrumbTrail;
use common\libraries\ActionBarRenderer;
use common\libraries\ActionBarSearchForm;
use common\libraries\Application;
use admin\AdminDataManager;
use admin\AdminManager;
use group\GroupManager;
use help\HelpManager;
use home\HomeManager;
use migration\MigrationManager;
use menu\MenuManager;
use repository\RepositoryManager;
use rights\RightsManager;
use tracking\TrackingManager;
use user\UserManager;
use webservice\WebserviceManager;

/**
 * $Id: browser.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_manager.component
 * @author Michael Kyndt
 */
class ReportingManagerBrowserComponent extends ReportingManager implements AdministrationComponent
{

    private $action_bar;
    private $application;

    const PARAM_SELECTED_APPLICATION = 'selected_app';

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $application = $this->application = Request :: get(self :: PARAM_SELECTED_APPLICATION);

        if (!$application)
            $application = $this->application = 'admin';

        if (!$this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get('NotAllowed', null, Utilities :: COMMON_LIBRARIES));
            $this->display_footer();
            exit();
        }

        $this->action_bar = $this->get_action_bar();
        //$output = $this->get_template_html();


        $this->display_header();
        echo '<br />' . $this->action_bar->as_html() . '<br />';
        echo '<div id="applications" class="applications">';
        echo BasicApplication :: get_selecter($this->get_url(array(self :: PARAM_SELECTED_APPLICATION => BasicApplication :: PLACEHOLDER_APPLICATION)), $application);
        if (isset($application))
        {
            echo $this->get_template_html();
        }
        else
        {
            echo $this->get_templates();
        }
        echo '</div>';
        $this->display_footer();
    }

    public static function get_application_platform_admin_links()
    {
        $info = array();

        $info[] = AdminManager :: get_application_platform_admin_links();
        $info[] = RepositoryManager :: get_application_platform_admin_links();
        $info[] = UserManager :: get_application_platform_admin_links();
        $info[] = RightsManager :: get_application_platform_admin_links();
        $info[] = GroupManager :: get_application_platform_admin_links();
        $info[] = WebserviceManager :: get_application_platform_admin_links();
        $info[] = TrackingManager :: get_application_platform_admin_links();
        $info[] = ReportingManager :: get_application_platform_admin_links();
        $info[] = HomeManager :: get_application_platform_admin_links();
        $info[] = MenuManager :: get_application_platform_admin_links();
        $info[] = MigrationManager :: get_application_platform_admin_links();
        $info[] = HelpManager :: get_application_platform_admin_links();

        //The links for the plugin applications running on top of the essential Chamilo components
        $applications = WebApplication :: load_all();
        foreach ($applications as $index => $application_name)
        {
            $info[] = call_user_func(array(
                        'application\\' . $application_name . '\\' . WebApplication :: get_application_class_name($application_name),
                        'get_application_platform_admin_links'));
        }

        return $info;
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

//        $html[] = '<script type="text/javascript" src="' . BasicApplication :: get_application_resources_javascript_path(ReportingManager :: APPLICATION_NAME) . 'reporting_browser.js' . '"></script>';
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

        $action_bar->set_search_url($this->get_url(array(
                    ReportingManager :: PARAM_TEMPLATE_ID => $this->get_reporting_template())));
        //$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path().'action_add.png', $this->get_url(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_CREATE_ROLE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));


        return $action_bar;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('reporting_browser');
    }

    function get_additional_parameters()
    {
        return array(ReportingManager :: PARAM_TEMPLATE_ID, self :: PARAM_SELECTED_APPLICATION);
    }

}

?>