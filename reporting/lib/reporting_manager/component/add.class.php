<?php
/**
 * $Id: add.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_manager.component
 * @author Michael Kyndt
 */
/**
 *
 */
class ReportingManagerAddComponent extends ReportingManager
{
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => ReportingManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Reporting')));
        $trail->add(new Breadcrumb($this->get_url(array(Translation :: get('Reporting')))));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddTemplate')));
        $trail->add_help('reporting general');

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
        //$table = new RoleBrowserTable($this, array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_BROWSE_ROLES), $this->get_condition());


        $html = array();
        $html[] = '<div style="float: right; width: 100%;">';
        //$html[] = $table->as_html();
        $html[] = 'bla';
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

    function get_template()
    {
        return (Request :: get(ReportingManager :: PARAM_TEMPLATE_ID) ? Request :: get(ReportingManager :: PARAM_TEMPLATE_ID) : 0);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(ReportingManager :: PARAM_TEMPLATE_ID => $this->get_template())));
        //$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path().'action_add.png', $this->get_url(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_CREATE_ROLE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));


        return $action_bar;
    }
}
?>