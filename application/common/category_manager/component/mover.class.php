<?php
/**
 * $Id: mover.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.category_manager.component
 */
require_once dirname(__FILE__) . '/../category_manager_component.class.php';
require_once dirname(__FILE__) . '/../platform_category.class.php';
require_once dirname(__FILE__) . '/../category_form.class.php';

class CategoryManagerMoverComponent extends CategoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_id = Request :: get(CategoryManager :: PARAM_CATEGORY_ID);
        $direction = Request :: get(CategoryManager :: PARAM_DIRECTION);
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('Browse Categories')));
        $trail->add(new Breadcrumb($this->get_url(array(CategoryManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('MoveCategory')));
        
        $user = $this->get_user();
        
        if (! isset($user) || ! isset($category_id))
        {
            Display :: not_allowed($trail);
            exit();
        }
        
        $categories = $this->retrieve_categories(new EqualityCondition(PlatformCategory :: PROPERTY_ID, $category_id));
        $category = $categories->next_result();
        $parent = $category->get_parent();
        
        $max = $this->count_categories(new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $parent));
        
        $display_order = $category->get_display_order();
        $new_place = $display_order + $direction;
        
        $succes = false;
        
        if($new_place > 0 && $new_place <= $max)
        {
	        $category->set_display_order($new_place);
	        
	        $conditions[] = new EqualityCondition(PlatformCategory :: PROPERTY_DISPLAY_ORDER, $new_place);
	        $conditions[] = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $parent);
	        $condition = new AndCondition($conditions);
	        $categories = $this->retrieve_categories($condition);
	        $newcategory = $categories->next_result();
	        
	        $newcategory->set_display_order($display_order);
	        
	        if ($category->update() && $newcategory->update())
	        {
	            $sucess = true;
	        }
        }

        $this->redirect(Translation :: get($sucess ? 'CategoryMoved' : 'CategoryNotMoved'), ($sucess ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $category->get_parent()));
    }
}
?>