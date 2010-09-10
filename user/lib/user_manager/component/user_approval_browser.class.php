<?php
/**
 * $Id: admin_user_browser.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

require_once dirname(__FILE__) . '/user_approver.class.php';

class UserManagerUserApprovalBrowserComponent extends UserManager implements AdministrationComponent
{
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (!UserRights :: is_allowed(UserRights :: VIEW_RIGHT, UserRights :: LOCATION_APPROVER_BROWSER, UserRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        $this->ab = $this->get_action_bar();
        $output = $this->get_user_html();

        $this->display_header();

        echo $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_user_html()
    {
        $parameters = $this->get_parameters(true);
		$parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
		
        $table = new UserApprovalBrowserTable($this, $parameters, $this->get_condition());

        $html = array();
        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_condition()
    {
        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
            return new OrCondition($or_conditions);
        }
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        return $action_bar;
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('user_approval_browser');
    }
}
?>