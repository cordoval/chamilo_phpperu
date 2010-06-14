<?php
/**
 * $Id: browser.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager.component
 */
require_once dirname(__FILE__) . '/rights_template_browser_table/rights_template_browser_table.class.php';
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class RightsTemplateManagerBrowserComponent extends RightsTemplateManager
{
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => RightsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Rights')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES)), Translation :: get('RightsTemplates')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseRightsTemplates')));
        $trail->add_help('rights general');

        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        $this->action_bar = $this->get_action_bar();
        $output = $this->get_user_html();

        $this->display_header();
        echo '<br />' . $this->action_bar->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_user_html()
    {
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        
    	$table = new RightsTemplateBrowserTable($this, $parameters, $this->get_condition());

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
            $condition = new PatternMatchCondition(HelpItem :: PROPERTY_NAME, '*' . $query . '*');
        }

        return $condition;
    }

    function get_rights_template()
    {
        return (Request :: get(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID) ? Request :: get(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID) : 0);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID => $this->get_rights_template())));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('NewRightsTemplate'), Theme :: get_image_path() . 'action_add_template.png', $this->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_CREATE_RIGHTS_TEMPLATE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ManageRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_CONFIGURE_RIGHTS_TEMPLATES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
}
?>