<?php
/**
 * $Id: subscribe_user_browser.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_manager.component
 */

class InternshipPlannerCategoryManagerSubscribeLocationBrowserComponent extends InternshipPlannerCategoryManagerComponent
{
    private $category;
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => InternshipPlannerCategoryManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('InternshipPlannerCategory')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_BROWSE_CATEGORIES)), Translation :: get('InternshipPlannerCategoryList')));
        $trail->add_help('category subscribe users');

        $category_id = Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID);

        if (isset($category_id))
        {
            $this->category = $this->retrieve_category($category_id);
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $category_id)), $this->category->get_name()));
        }

        $trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('AddLocations')));

        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail);
            Display :: error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        $this->ab = $this->get_action_bar();
        $output = $this->get_user_subscribe_html();

        $this->display_header($trail);
        echo $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_user_subscribe_html()
    {
        $table = new SubscribeLocationBrowserTable($this, array(Application :: PARAM_APPLICATION => InternshipPlannerCategoryManager :: APPLICATION_NAME, Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_SUBSCRIBE_LOCATION_BROWSER, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $this->category->get_id()), $this->get_subscribe_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function get_subscribe_condition()
    {
        $condition = new EqualityCondition(InternshipPlannerCategoryRelLocation :: PROPERTY_CATEGORY_ID, Request :: get(InternshipPlannerCategoryRelLocation :: PROPERTY_CATEGORY_ID));

        $users = $this->get_parent()->retrieve_category_rel_users($condition);

        $conditions = array();
        while ($user = $users->next_result())
        {
            $conditions[] = new NotCondition(new EqualityCondition(Location :: PROPERTY_ID, $user->get_user_id()));
        }

        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(Location :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(Location :: PROPERTY_LASTNAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(Location :: PROPERTY_LOCATIONNAME, '*' . $query . '*');
            $conditions[] = new OrCondition($or_conditions);
        }

        if (count($conditions) == 0)
            return null;

        $condition = new AndCondition($conditions);

        return $condition;
    }

    function get_category()
    {
        return $this->category;
    }

    function get_action_bar()
    {
        $category = $this->category;

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $category->get_id())));

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $category->get_id())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowInternshipPlannerCategory'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_BROWSE_CATEGORIES, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $category->get_id()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));


        return $action_bar;
    }
}
?>