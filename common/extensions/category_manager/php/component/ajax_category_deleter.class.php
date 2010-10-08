<?php
/**
 * $Id: ajax_category_deleter.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.category_manager.component
 */
require_once dirname(__FILE__) . '/../category_manager_component.class.php';

class CategoryManagerAjaxCategoryDeleterComponent extends CategoryManagerComponent
{

    function run()
    {
        $id = $_POST['item'];
        
        if (! isset($id) || ! $this->allowed_to_delete_category($id))
        {
            echo "false";
            exit();
        }
        
        $category = $this->retrieve_categories(new EqualityCondition('id', $id))->next_result();
        $bool = $category->delete();
        $bool &= $this->delete_children($id);
        
        if ($bool)
            echo "true";
        else
            echo "false";
    }

    function delete_children($id)
    {
        $bool = true;
        
        $categories = $this->retrieve_categories(new EqualityCondition('parent_id', $id));
        while ($category = $categories->next_result())
        {
            $bool &= $category->delete();
            $bool &= $this->delete_children($category->get_id());
        }
        
        return $bool;
    }
}
?>