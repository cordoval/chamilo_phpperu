<?php
/**
 * $Id: learningobjectpublicationcategorytree.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser
 */
require_once 'HTML/Menu.php';
/**
 * A tree menu to display categories in a tool
 */
class ContentObjectPublicationCategoryTree extends HTML_Menu
{
    const TREE_NAME = __CLASS__;
    
    /**
     * The browser to which this category tree is associated
     */
    private $browser;
    /**
     * An id for this tree
     */
    private $tree_id;
    
    private $data_manager;
    
    private $url_params;

    /**
     * Create a new category tree
     * @param PublicationBrowser $browser The browser to associate this category
     * tree with.
     * @param string $tree_id An id for the tree
     */
    function ContentObjectPublicationCategoryTree($browser, $tree_id, $url_params = array())
    {
        $this->browser = $browser;
        $this->tree_id = $tree_id;
        $this->url_params = $url_params;
        $this->data_manager = WeblcmsDataManager :: get_instance();
        $menu = $this->get_menu_items();
        parent :: __construct($menu);
        $this->forceCurrentUrl($this->get_category_url($tree_id));
    }

	function as_html()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }
    
    static function get_tree_name()
    {
    	return Utilities :: camelcase_to_underscores(self :: TREE_NAME);
    }

    /**
     * Gets the current selected category id.
     * @return int The current category id
     */
    function get_current_category_id()
    {
        return intval(Request :: get($this->tree_id));
    }

    private function get_menu_items($extra_items)
    {
        $menu = array();
        $menu_item = array();
        $menu_item['title'] = Translation :: get('Root') . $this->get_category_count(0);
        $menu_item['url'] = $this->get_category_url(0);
        $sub_menu_items = $this->get_sub_menu_items(0);
        if (count($sub_menu_items) > 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }
        $menu_item['class'] = 'type_category';
        $menu_item[OptionsMenuRenderer :: KEY_ID] = 0;
        $menu[0] = $menu_item;
        if (count($extra_items))
        {
            $menu = array_merge($menu, $extra_items);
        }
        
        return $menu;
    }

    private function get_sub_menu_items($parent)
    {
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_PARENT, $parent);
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $this->browser->get_parent()->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_TOOL, $this->browser->get_parent()->get_tool_id());
        $condition = new AndCondition($conditions);
        
        $objects = $this->data_manager->retrieve_content_object_publication_categories($condition);
        $categories = array();
        while ($category = $objects->next_result())
        {
            $menu_item = array();
            $menu_item['title'] = $category->get_name() . $this->get_category_count($category->get_id());
            $menu_item['url'] = $this->get_category_url($category->get_id());
            $sub_menu_items = $this->get_sub_menu_items($category->get_id());
            if (count($sub_menu_items) > 0)
            {
                $menu_item['sub'] = $sub_menu_items;
            }
            $menu_item['class'] = 'type_category';
            $menu_item[OptionsMenuRenderer :: KEY_ID] = $category->get_id();
            $categories[$category->get_id()] = $menu_item;
        }
        return $categories;
    }

    private function get_category_count($category_id)
    {
        $count = $this->get_publication_count($category_id);
        return ($count > 0) ? ' (' . $count . ')' : '';
    }

    private function get_publication_count($category)
    {
        $dm = WeblcmsDataManager :: get_instance();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->browser->get_parent()->get_course_id());
        $conditions[] = $this->get_condition($category);
        
    	$user_id = $this->browser->get_user_id();
        $course_groups = $this->browser->get_course_groups();
                
        $course_group_ids = array();
                
        foreach($course_groups as $course_group)
        {
           	$course_group_ids[] = $course_group->get_id();
        }
        
       /* $access = array();
        $access[] = new InCondition('user_id', $user_id, $dm->get_alias('content_object_publication_user'));
        $access[] = new InCondition('course_group_id', $course_group_ids, $dm->get_alias('content_object_publication_course_group'));
        if (! empty($user_id) || ! empty($course_group_ids))
        {
            $access[] = new AndCondition(array(new EqualityCondition('user_id', null, $dm->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $dm->get_alias('content_object_publication_course_group'))));
        }
        */
        
    	$access = array();
        if($user_id)
        {
    		$access[] = new InCondition(ContentObjectPublicationUser :: PROPERTY_USER, $user_id, ContentObjectPublicationUser :: get_table_name());
        }
    	
    	if(count($course_group_ids) > 0)
    	{
        	$access[] = new InCondition(ContentObjectPublicationCourseGroup :: PROPERTY_COURSE_GROUP_ID, $course_group_ids, ContentObjectPublicationCourseGroup :: get_table_name());
    	}
        	
        if (! empty($user_id) || ! empty($course_group_ids))
        {
            $access[] = new AndCondition(array(
            			new EqualityCondition(ContentObjectPublicationUser :: PROPERTY_USER, null, ContentObjectPublicationUser :: get_table_name()), 
            			new EqualityCondition(ContentObjectPublicationCourseGroup :: PROPERTY_COURSE_GROUP_ID, null, ContentObjectPublicationCourseGroup :: get_table_name())));
        }
        
        $conditions[] = new OrCondition($access);
        $subselect_condition = new InCondition('type', $this->browser->get_allowed_types());
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
        
        $condition = new AndCondition($conditions);
        
        return $dm->count_content_object_publications_new($condition);
    }

    private function get_condition($category = null)
    {
        if (is_null($category))
        {
            $category = $this->get_current_category_id();
        }
        $tool_cond = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, $this->browser->get_parent()->get_tool_id());
        $category_cond = new EqualityCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $category);
        return new AndCondition($tool_cond, $category_cond);
    }

    /**
     * Gets the URL of a category
     * @param int $category_id The id of the category of which the URL is
     * requested
     * @return string The URL
     */
    private function get_category_url($category_id)
    {
        $this->url_params['pcattree'] = $category_id;
        return $this->browser->get_url($this->url_params);
    }

    function get_breadcrumbs()
    {
        $array_renderer = new HTML_Menu_ArrayRenderer();
        $this->render($array_renderer, 'urhere');
        $breadcrumbs = $array_renderer->toArray();
        foreach ($breadcrumbs as &$crumb)
        {
            $split = explode('(', $crumb['title']);
            $crumb['title'] = $split[0];
        }
        return $breadcrumbs;
    }
}
?>