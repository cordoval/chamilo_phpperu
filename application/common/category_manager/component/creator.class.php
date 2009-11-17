<?php
/**
 * $Id: creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.category_manager.component
 */
require_once dirname(__FILE__) . '/../category_manager_component.class.php';
require_once dirname(__FILE__) . '/../platform_category.class.php';
require_once dirname(__FILE__) . '/../category_form.class.php';

class CategoryManagerCreatorComponent extends CategoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = $this->get_breadcrumb_trail();
        
        if (Request :: get(CategoryManager :: PARAM_CATEGORY_ID))
        {
            require_once dirname(__FILE__) . '/../category_menu.class.php';
            $menu = new CategoryMenu(Request :: get(CategoryManager :: PARAM_CATEGORY_ID), $this->get_parent());
            $trail->merge($menu->get_breadcrumbs());
        }
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddCategory')));
        
        $category_id = Request :: get(CategoryManager :: PARAM_CATEGORY_ID);
        $user = $this->get_user();
        
        $category = $this->get_category();
        $category->set_parent(isset($category_id) ? $category_id : 0);
        
        $form = new CategoryForm(CategoryForm :: TYPE_CREATE, $this->get_url(array(CategoryManager :: PARAM_CATEGORY_ID => $category_id)), $category, $user, $this);
        
        if ($form->validate())
        {
            $success = $form->create_category();
            //if(get_class($this->get_parent()) == 'RepositoryCategoryManager')
            //$this->repository_redirect(RepositoryManager :: ACTION_MANAGE_CATEGORIES, Translation :: get($success ? 'CategoryCreated' : 'CategoryNotCreated'), 0, ($success ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $category->get_id()));
            //else
            //$this->redirect(Translation :: get($success ? 'CategoryCreated' : 'CategoryNotCreated'), ($success ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $category->get_id()));
            $this->redirect(Translation :: get($success ? 'CategoryCreated' : 'CategoryNotCreated'), ($success ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => 0));
        }
        else
        {
            $this->display_header($trail);
            echo '<br />';
            $form->display();
            $this->display_footer();
        }
    }
}
?>