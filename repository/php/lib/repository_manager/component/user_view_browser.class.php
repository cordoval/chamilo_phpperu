<?php
namespace repository;

use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;

/**
 * $Id: user_view_browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class RepositoryManagerUserViewBrowserComponent extends RepositoryManager
{
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        /*if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail, false, true);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }*/

        $this->ab = $this->get_action_bar();

        $output = $this->get_user_html();

        $this->display_header(null, false, true);
        echo '<br />' . $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_user_html()
    {
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();

    	$table = new UserViewBrowserTable($this, $parameters, $this->get_condition());

        $html = array();
        $html[] = '<div style="float: right; width:100%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_condition()
    {
        $condition = new EqualityCondition(UserView :: PROPERTY_USER_ID, $this->get_user_id());

        $query = $this->ab->get_query();
        if (isset($query) && $query != '')
        {
            $or_conditions = array();
            $or_conditions[] = new PatternMatchCondition(UserView :: PROPERTY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(UserView :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $or_condition = new OrCondition($or_conditions);

            $and_conditions[] = array();
            $and_conditions = $condition;
            $and_conditions = $or_condition;
            $condition = new AndCondition($and_conditions);
        }

        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_user_view_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_user_view_browser');
    }
}
?>