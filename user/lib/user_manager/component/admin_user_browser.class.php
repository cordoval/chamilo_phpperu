<?php
/**
 * $Id: admin_user_browser.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerAdminUserBrowserComponent extends UserManager implements AdministrationComponent
{
    private $firstletter;
    private $menu_breadcrumbs;
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->firstletter = Request :: get(UserManager :: PARAM_FIRSTLETTER);

        if (!UserRights :: is_allowed_in_users_subtree(UserRights :: VIEW_RIGHT, 0))
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
        //echo $menu;
        echo $output;
        $this->display_footer();
    }

    function get_user_html()
    {
        $parameters = $this->get_parameters(true);
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();

        $table = new AdminUserBrowserTable($this, $parameters, $this->get_condition());

        $html = array();
        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_menu_html()
    {
        $extra_items = array();
        /*if ($this->get_search_validate())
		{
			// $search_url = $this->get_url();
			$search_url = '#';
			$search = array ();
			$search['title'] = Translation :: get('SearchResults');
			$search['url'] = $search_url;
			$search['class'] = 'search_results';
			$extra_items[] = $search;
		}
		else
		{
			$search_url = null;
		}*/

        $temp_replacement = '__FIRSTLETTER__';
        $url_format = $this->get_url(array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS, UserManager :: PARAM_FIRSTLETTER => $temp_replacement));
        $url_format = str_replace($temp_replacement, '%s', $url_format);
        $user_menu = new UserMenu($this->firstletter, $url_format, $extra_items);
        $this->menu_breadcrumbs = $user_menu->get_breadcrumbs();

        $html = array();
        $html[] = '<div style="float: left; width: 20%;">';
        $html[] = $user_menu->render_as_tree();
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
            $search_conditions = new OrCondition($or_conditions);
        }

        $condition = null;
        if (isset($this->firstletter))
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, $this->firstletter . '*');
            $conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, chr(ord($this->firstletter) + 1) . '*');
            $conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, chr(ord($this->firstletter) + 2) . '*');
            $condition = new OrCondition($conditions);
            if (count($search_conditions))
            {
                $condition = new AndCondition($condition, $search_conditions);
            }
        }
        else
        {
            if (count($search_conditions))
            {
                $condition = $search_conditions;
            }
        }
        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());

        if(UserRights :: is_allowed_in_users_subtree(UserRights :: ADD_RIGHT, 0))
        {
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path() . 'action_add.png', $this->get_url(array(Application :: PARAM_ACTION => UserManager :: ACTION_CREATE_USER)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));	
        }
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        return $action_bar;
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('user_browser');
    }
}
?>