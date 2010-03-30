<?php
/**
 * $Id: parent_changer.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.category_manager.component
 */
require_once dirname(__FILE__) . '/../category_manager_component.class.php';
require_once dirname(__FILE__) . '/../platform_category.class.php';

class CategoryManagerParentChangerComponent extends CategoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();
        
        $ids = Request :: get(CategoryManager :: PARAM_CATEGORY_ID);
        $this->get_breadcrumb_trail()->add(new Breadcrumb($this->get_url(array(CategoryManager :: PARAM_CATEGORY_ID => $ids)), Translation :: get('MoveCategories')));
        
        if (! $user)
        {
            $this->display_header($this->get_breadcrumb_trail());
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        if (! is_array($ids))
        {
            $ids = array($ids);
        }
        
        if (count($ids) != 0)
        {
            $bool = true;
            $parent = $this->retrieve_categories(new EqualityCondition(PlatformCategory :: PROPERTY_ID, $ids[0]))->next_result()->get_parent();
            
            $form = $this->get_move_form($ids, $parent);
            
            $success = true;
            
            $categories = array();
            
        	foreach ($ids as $id)
            {
                $categories[] = $this->retrieve_categories(new EqualityCondition(PlatformCategory :: PROPERTY_ID, $id))->next_result();
            }
            
            if ($form->validate())
            {
                $new_parent = $form->exportValue('category');
                foreach ($categories as $category)
                {
                    $category->set_parent($new_parent);
                    $category->set_display_order($this->get_next_category_display_order($new_parent));
                    $success &= $category->update();
                }
                
                $this->clean_display_order_old_parent($parent);
                
                /*if(get_class($this->get_parent()) == 'RepositoryCategoryManager')
					$this->repository_redirect(RepositoryManager :: ACTION_MANAGE_CATEGORIES, Translation :: get($success ? 'CategoryCreated' : 'CategoryNotCreated'), 0, ($success ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $parent));
				else*/
                $this->redirect(Translation :: get($success ? 'CategoryMoved' : 'CategoryNotMoved'), ($success ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $parent));
            }
            else
            {
                $this->display_header($this->get_breadcrumb_trail());
                
                echo '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'action_category.png);">';
                echo '<div class="title">' . Translation :: get('SelectedCategories');
                echo '</div>';
                echo '<div class="description">';
                echo '<ul>';
                
                foreach ($categories as $category)
                {
                	echo '<li>' . $category->get_name() . '</li>';
                }
                
                echo '</ul></div><div class="clear"></div></div>';
                
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_header($this->get_breadcrumb_trail());
            Display :: error_message(Translation :: get("NoObjectSelected"));
            $this->display_footer();
        }
    }
    private $tree;

    function get_move_form($selected_categories, $current_parent)
    {
        if ($current_parent != 0)
            $this->tree[0] = Translation :: get('Root');
        
        $this->build_category_tree(0, $selected_categories, $current_parent);
        $form = new FormValidator('select_category', 'post', $this->get_url(array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_CHANGE_CATEGORY_PARENT, CategoryManager :: PARAM_CATEGORY_ID => Request :: get(CategoryManager :: PARAM_CATEGORY_ID))));
        $form->addElement('select', 'category', Translation :: get('Category'), $this->tree);
        $form->addElement('submit', 'submit', Translation :: get('Ok'));
        return $form;
    }
    
    private $level = 1;

    function build_category_tree($parent_id, $selected_categories, $current_parent)
    {
        $conditions[] = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $parent_id);
        $conditions[] = new NotCondition(new EqualityCondition(PlatformCategory :: PROPERTY_ID, $current_parent));
        
        foreach ($selected_categories as $selected_category)
            $conditions[] = new NotCondition(new EqualityCondition(PlatformCategory :: PROPERTY_ID, $selected_category));
        
        $condition = new AndCondition($conditions);
        
        $categories = $this->retrieve_categories($condition);
        
        $tree = array();
        while ($cat = $categories->next_result())
        {
            $this->tree[$cat->get_id()] = str_repeat('--', $this->level) . ' ' . $cat->get_name();
            $this->level ++;
            $this->build_category_tree($cat->get_id(), $selected_categories, $current_parent);
            $this->level --;
        }
    }

    function clean_display_order_old_parent($parent)
    {
        $condition = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $parent);
        
        $categories = $this->retrieve_categories($condition, null, null, array(new ObjectTableOrder('display_order')));
        
        $i = 1;
        
        while ($cat = $categories->next_result())
        {
            $cat->set_display_order($i);
            $cat->update();
            $i++;
        }
    }

}
?>