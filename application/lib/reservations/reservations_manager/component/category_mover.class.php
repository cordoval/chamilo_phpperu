<?php
/**
 * $Id: category_mover.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';

require_once dirname(__FILE__) . '/../../category.class.php';
require_once dirname(__FILE__) . '/../../forms/category_form.class.php';
require_once dirname(__FILE__) . '/../../reservations_data_manager.class.php';

class ReservationsManagerCategoryMoverComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_id = $_GET[ReservationsManager :: PARAM_CATEGORY_ID];
        $direction = $_GET[ReservationsManager :: PARAM_DIRECTION];
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_CATEGORIES, ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('ManageCategories')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('MoveCategory')));
        
        $user = $this->get_user();
        
        if (! isset($user) || ! isset($category_id))
        {
            Display :: not_allowed($trail);
            exit();
        }
        
        $categories = $this->retrieve_categories(new EqualityCondition(Category :: PROPERTY_ID, $category_id));
        $category = $categories->next_result();
        $parent = $category->get_parent();
        
        $display_order = $category->get_display_order();
        $new_place = $display_order + $direction;
        $category->set_display_order($new_place);
        
        $conditions[] = new EqualityCondition(Category :: PROPERTY_DISPLAY_ORDER, $new_place);
        $conditions[] = new EqualityCondition(Category :: PROPERTY_PARENT, $parent);
        $condition = new AndCondition($conditions);
        $categories = $this->retrieve_categories($condition);
        $newcategory = $categories->next_result();
        
        $newcategory->set_display_order($display_order);
        
        $sucess = true;
        
        if (! $category->update() || ! $newcategory->update())
        {
            $sucess = false;
        }
        
        Event :: trigger('move_category', 'reservations', array('target_id' => $category_id, 'user_id' => $this->get_user_id()));
        
        $this->redirect(Translation :: get($sucess ? 'CategoryMoved' : 'CategoryNotMoved'), ($sucess ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_CATEGORIES, ReservationsManager :: PARAM_CATEGORY_ID => $category->get_parent()));
    }
}
?>