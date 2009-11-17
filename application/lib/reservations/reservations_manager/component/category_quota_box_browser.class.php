<?php
/**
 * $Id: category_quota_box_browser.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';
require_once dirname(__FILE__) . '/../reservations_manager_component.class.php';
require_once dirname(__FILE__) . '/category_quota_box_browser/category_quota_box_browser_table.class.php';

class ReservationsManagerCategoryQuotaBoxBrowserComponent extends ReservationsManagerComponent
{
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        
        $category_id = $this->get_category_id();
        
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID, $category_id)), Translation :: get('ViewCategoryQuotaBoxes')));
        
        $this->ab = $this->get_action_bar();
        
        $this->display_header($trail);
        echo $this->ab->as_html() . '<br />';
        echo $this->get_user_html();
        $this->display_footer();
    }

    function get_user_html()
    {
        $parameters = $this->get_parameters();
        $parameters = array_merge($parameters, array(array(ReservationsManager :: PARAM_CATEGORY_ID, $this->get_category_id())));
        $table = new CategoryQuotaBoxBrowserTable($this, $parameters, $this->get_condition());
        
        $html = array();
        $html[] = $table->as_html();
        
        return implode($html, "\n");
    }

    function get_category_id()
    {
        $id = Request :: get(ReservationsManager :: PARAM_CATEGORY_ID);
        if (! isset($id) || is_null($id))
            $id = 0;
        
        return $id;
    }

    function get_condition()
    {
        return new EqualityCondition(QuotaBoxRelCategory :: PROPERTY_CATEGORY_ID, $this->get_category_id());
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        //$action_bar->set_search_url($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $this->get_category())));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_category_quota_box_url($this->get_category_id()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }
}