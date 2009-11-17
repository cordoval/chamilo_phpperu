<?php
/**
 * $Id: sorter.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.lib.menu_manager.component
 */
/**
 * Weblcms component allows the user to manage course categories
 */
class MenuManagerSorterComponent extends MenuManagerComponent
{
    private $category;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Header :: set_section('admin');
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU)), Translation :: get('Menu')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('MenuSort')));
        $trail->add_help('menu general');
        
        $user = $this->get_user();
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail);
            Display :: error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->category = Request :: get(MenuManager :: PARAM_CATEGORY);
        $component_action = Request :: get(MenuManager :: PARAM_COMPONENT_ACTION);
        
        switch ($component_action)
        {
            case 'edit' :
                $this->edit_navigation_item();
                break;
            case 'delete' :
                $this->delete_navigation_item();
                break;
            case 'add' :
                $this->add_navigation_item();
                break;
            case 'add_category' :
                $this->add_category_navigation_item();
            case 'edit_category' :
                $this->edit_category_navigation_item();
            case 'move' :
                $this->move_navigation_item();
                break;
            default :
                $this->show_navigation_item_list();
        }
    }
    
    private $action_bar;

    function show_navigation_item_list()
    {
        $this->action_bar = $this->get_action_bar();
        
        $parameters = $this->get_parameters(true);
        
        $table = new NavigationItemBrowserTable($this, $parameters, $this->get_condition());
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU)), Translation :: get('Menu')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('SortMenuManagerCategories')));
        $trail->add_help('menu general');
        
        $this->display_header($trail);
        
        echo $this->action_bar->as_html();
        
        echo '<div style="float: left; width: 12%; overflow:auto;">';
        echo $this->get_menu()->render_as_tree();
        echo '</div>';
        echo '<div style="float: right; width: 85%;">';
        echo $table->as_html();
        echo '</div>';
        $this->display_footer();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $category = (isset($this->category) ? $this->category : 0);
        $action_bar->set_search_url($this->get_url(array('category' => $category)));
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddItem'), Theme :: get_common_image_path() . 'action_create.png', $this->get_navigation_item_creation_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddCategory'), Theme :: get_common_image_path() . 'action_category.png', $this->get_category_navigation_item_creation_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array('category' => $category)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }

    function move_navigation_item()
    {
        $direction = Request :: get(MenuManager :: PARAM_DIRECTION);
        $category = Request :: get(MenuManager :: PARAM_CATEGORY);
        
        if (isset($direction) && isset($category))
        {
            $move_category = $this->retrieve_navigation_item($category);
            $sort = $move_category->get_sort();
            $next_category = $this->retrieve_navigation_item_at_sort($move_category->get_category(), $sort, $direction);
            
            if ($direction == 'up')
            {
                $move_category->set_sort($sort - 1);
                $next_category->set_sort($sort);
            }
            elseif ($direction == 'down')
            {
                $move_category->set_sort($sort + 1);
                $next_category->set_sort($sort);
            }
            
            if ($move_category->update() && $next_category->update())
            {
                $success = true;
            }
            else
            {
                $success = false;
            }
            
            $this->redirect(Translation :: get($success ? 'MenuManagerCategoryMoved' : 'MenuManagerCategoryNotMoved'), ($success ? false : true), array(MenuManager :: PARAM_COMPONENT_ACTION => MenuManager :: ACTION_COMPONENT_BROWSE_CATEGORY, MenuManager :: PARAM_CATEGORY => $move_category->get_category()));
        }
        else
        {
            $this->show_navigation_item_list();
        }
    }

    function add_navigation_item()
    {
        $menucategory = new NavigationItem();
        
        $menucategory->set_application('');
        $menucategory->set_category(0);
        
        $form = new NavigationItemForm(NavigationItemForm :: TYPE_CREATE, $menucategory, $this->get_url(array(MenuManager :: PARAM_COMPONENT_ACTION => MenuManager :: ACTION_COMPONENT_ADD_CATEGORY)));
        
        if ($form->validate())
        {
            $success = $form->create_navigation_item();
            $this->redirect(Translation :: get($success ? 'MenuManagerCategoryAdded' : 'MenuManagerCategoryNotAdded'), ($success ? false : true), array(MenuManager :: PARAM_CATEGORY => $form->get_navigation_item()->get_category()));
        }
        else
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU)), Translation :: get('Menu')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddMenuManagerItem')));
            $trail->add_help('menu general');
            
            $this->display_header($trail);
            echo '<div style="float: left; width: 12%; overflow:auto;">';
            echo $this->get_menu()->render_as_tree();
            echo '</div>';
            echo '<div style="float: right; width: 85%;">';
            $form->display();
            echo '</div>';
            $this->display_footer();
        }
    }

    function add_category_navigation_item()
    {
        $menucategory = new NavigationItem();
        $form = new NavigationItemCategoryForm(NavigationItemCategoryForm :: TYPE_CREATE, $menucategory, $this->get_url(array(MenuManager :: PARAM_COMPONENT_ACTION => MenuManager :: ACTION_COMPONENT_CAT_ADD)));
        
        if ($form->validate())
        {
            $success = $form->create_navigation_item();
            $this->redirect(Translation :: get($success ? 'MenuManagerCategoryAdded' : 'MenuManagerCategoryNotAdded'), ($success ? false : true), array(MenuManager :: PARAM_CATEGORY => $form->get_navigation_item()->get_category()));
        }
        else
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU)), Translation :: get('Menu')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddMenuManagerCategory')));
            $trail->add_help('menu general');
            
            $this->display_header($trail);
            echo '<div style="float: left; width: 12%; overflow:auto;">';
            echo $this->get_menu()->render_as_tree();
            echo '</div>';
            echo '<div style="float: right; width: 85%;">';
            $form->display();
            echo '</div>';
            $this->display_footer();
        }
    }

    function edit_navigation_item()
    {
        $menucategory = $this->retrieve_navigation_item($this->category);
        
        $form = new NavigationItemForm(NavigationItemForm :: TYPE_EDIT, $menucategory, $this->get_url(array(MenuManager :: PARAM_COMPONENT_ACTION => MenuManager :: ACTION_COMPONENT_EDIT_CATEGORY, MenuManager :: PARAM_CATEGORY => $menucategory->get_id())));
        
        if ($form->validate())
        {
            $success = $form->update_navigation_item();
            $this->redirect(Translation :: get($success ? 'MenuManagerCategoryUpdated' : 'MenuManagerCategoryNotUpdated'), ($success ? false : true), array(MenuManager :: PARAM_CATEGORY => $form->get_navigation_item()->get_category()));
        }
        else
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU)), Translation :: get('Menu')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateMenuManagerItem')));
            $trail->add_help('menu general');
            
            $this->display_header($trail);
            echo '<div style="float: left; width: 12%; overflow:auto;">';
            echo $this->get_menu()->render_as_tree();
            echo '</div>';
            echo '<div style="float: right; width: 85%;">';
            $form->display();
            echo '</div>';
            $this->display_footer();
        }
    }

    function edit_category_navigation_item()
    {
        $menucategory = $this->retrieve_navigation_item($this->category);
        
        $form = new NavigationItemCategoryForm(NavigationItemCategoryForm :: TYPE_EDIT, $menucategory, $this->get_url(array(MenuManager :: PARAM_COMPONENT_ACTION => MenuManager :: ACTION_COMPONENT_CAT_EDIT, MenuManager :: PARAM_CATEGORY => $menucategory->get_id())));
        
        if ($form->validate())
        {
            $success = $form->update_navigation_item();
            $this->redirect(Translation :: get($success ? 'MenuManagerCategoryUpdated' : 'MenuManagerCategoryNotUpdated'), ($success ? false : true), array(MenuManager :: PARAM_CATEGORY => $form->get_navigation_item()->get_category()));
        }
        else
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU)), Translation :: get('Menu')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateMenuManagerCategory')));
            $trail->add_help('menu general');
            
            $this->display_header($trail);
            echo '<div style="float: left; width: 12%; overflow:auto;">';
            echo $this->get_menu()->render_as_tree();
            echo '</div>';
            echo '<div style="float: right; width: 85%;">';
            $form->display();
            echo '</div>';
            $this->display_footer();
        }
    }

    function delete_navigation_item()
    {
        $navigation_item_id = Request :: get(MenuManager :: PARAM_CATEGORY);
        $parent = 0;
        $failures = 0;
        
        if (! empty($navigation_item_id))
        {
            if (! is_array($navigation_item_id))
            {
                $navigation_item_id = array($navigation_item_id);
            }
            
            foreach ($navigation_item_id as $id)
            {
                $category = $this->retrieve_navigation_item($id);
                $parent = $category->get_category();
                
                if (! $category->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($navigation_item_id) == 1)
                {
                    $message = 'SelectedCategoryNotDeleted';
                }
                else
                {
                    $message = 'SelectedCategoriesNotDeleted';
                }
            }
            else
            {
                if (count($navigation_item_id) == 1)
                {
                    $message = 'SelectedCategoryDeleted';
                }
                else
                {
                    $message = 'SelectedCategoriesDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(MenuManager :: PARAM_COMPONENT_ACTION => MenuManager :: ACTION_COMPONENT_BROWSE_CATEGORY, MenuManager :: PARAM_CATEGORY => $parent));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoMenuManagerCategorySelected')));
        }
    }

    function get_condition()
    {
        $condition = null;
        $category = (isset($this->category) ? $this->category : 0);
        $condition = new EqualityCondition(NavigationItem :: PROPERTY_CATEGORY, $category);
        
        $search = $this->action_bar->get_query();
        if (isset($search) && $search != '')
        {
            $conditions[] = $condition;
            $conditions[] = new LikeCondition(NavigationItem :: PROPERTY_TITLE, $search);
            $condition = new AndCondition($conditions);
        }
        
        return $condition;
    }

    function get_menu()
    {
        if (! isset($this->menu))
        {
            /*$extra_items_after = array ();

			$create = array ();
			$create['title'] = Translation :: get('Add');
			$create['url'] = $this->get_navigation_item_creation_url();
			$create['class'] = 'create';
			$extra_items_after[] = & $create;*/
            
            $temp_replacement = '__CATEGORY__';
            $url_format = $this->get_url(array(Application :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU, MenuManager :: PARAM_CATEGORY => $temp_replacement));
            $url_format = str_replace($temp_replacement, '%s', $url_format);
            $this->menu = new NavigationItemMenu($this->category, $url_format, null, null);
            
            $component_action = Request :: get(MenuManager :: PARAM_COMPONENT_ACTION);
            
            if ($component_action == MenuManager :: ACTION_COMPONENT_ADD_CATEGORY)
            {
                $this->menu->forceCurrentUrl($this->get_navigation_item_creation_url(), true);
            }
            //			elseif(!isset($this->category))
        //			{
        //				$this->menu->forceCurrentUrl($this->get_menu_home_url(), true);
        //			}
        }
        return $this->menu;
    }

    function get_menu_home_url()
    {
        return $this->get_url(array(Application :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU));
    }
}
?>