<?php
/**
 * $Id: browser.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.category_manager.component
 */
require_once dirname(__FILE__) . '/../category_menu.class.php';
require_once dirname(__FILE__) . '/../category_manager_component.class.php';
require_once dirname(__FILE__) . '/../platform_category.class.php';
require_once dirname(__FILE__) . '/category_browser/category_browser_table.class.php';

class CategoryManagerBrowserComponent extends CategoryManagerComponent
{
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->set_parameter(CategoryManager :: PARAM_CATEGORY_ID, Request :: get(CategoryManager :: PARAM_CATEGORY_ID));
    	$trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('category_manager_browser');
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CategoryManagerBrowserComponent')));
        
    	$this->ab = $this->get_action_bar(); //new ActionBarRenderer($this->get_left_toolbar_data(), array(), );
        $menu = new CategoryMenu(Request :: get(CategoryManager :: PARAM_CATEGORY_ID), $this->get_parent());

        echo $this->display_header();
        echo $this->ab->as_html() . '<br />';
        
        if($this->get_subcategories_allowed())
        {
        	echo '<div style="float: left; padding-right: 20px; width: 18%; overflow: auto; height: 100%;">' . $menu->render_as_tree() . '</div>';
        }
        echo $this->get_user_html();
        echo $this->display_footer();
    }

    function get_user_html()
    {
        $parameters = array_merge($this->get_parameters(), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $this->get_category()));
        $table = new CategoryBrowserTable($this, $parameters, $this->get_condition());

        $html = array();
        
        if($this->get_subcategories_allowed())
        {
        	$html[] = '<div style="float: right; width: 80%;">';
        	$html[] = $table->as_html();
        	$html[] = '</div>';
        }
       	else
       	{
       		$html[] = $table->as_html();
       	}

        return implode($html, "\n");
    }

    function get_condition()
    {
        $cat_id = $this->get_category();
        $condition = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $cat_id);

        $search = $this->ab->get_query();
        if (isset($search) && ($search != ''))
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(PlatformCategory :: PROPERTY_NAME, '*' . $search . '*');
            $orcondition = new OrCondition($conditions);

            $conditions = array();
            $conditions[] = $orcondition;
            $conditions[] = $condition;
            $condition = new AndCondition($conditions);
        }

        return $condition;
    }

    function get_category()
    {
        return (Request :: get(CategoryManager :: PARAM_CATEGORY_ID) ? Request :: get(CategoryManager :: PARAM_CATEGORY_ID) : 0);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(CategoryManager :: PARAM_CATEGORY_ID => $this->get_category())));

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_category_url(Request :: get(CategoryManager :: PARAM_CATEGORY_ID)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $not_allowed = array('ContentObjectPublicationCategoryManager', 'AdminCategoryManager', 'RepositoryCategoryManager');
        if (! in_array(get_class($this->get_parent()), $not_allowed))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('CopyGeneralCategories'), Theme :: get_common_image_path() . 'treemenu_types/exercise.png', $this->get_copy_general_categories_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(CategoryManager :: PARAM_CATEGORY_ID => Request :: get(CategoryManager :: PARAM_CATEGORY_ID))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
}