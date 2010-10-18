<?php
/**
 * $Id: content_object_publication_category_manager.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.category_manager
 */
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';
require_once dirname(__FILE__) . '/content_object_publication_category.class.php';

class ContentObjectPublicationCategoryManager extends CategoryManager
{

    function ContentObjectPublicationCategoryManager($parent, $trail = null, $is_subcategories_allowed = true)
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($parent->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_MANAGE_CATEGORIES)), Translation :: get('ManageCategories')));
        parent :: __construct($parent, $trail, $is_subcategories_allowed);
    }

    function get_category()
    {
        $category = new ContentObjectPublicationCategory();
        $category->set_tool($this->get_parent()->get_tool_id());
        $category->set_course($this->get_parent()->get_course_id());
        $category->set_allow_change(1);
        return $category;
    }

    function allowed_to_delete_category($category_id)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        
        $category = $wdm->retrieve_content_object_publication_categories(new EqualityCondition('id', $category_id))->next_result();
        if ($category)
        {
            if ($category->get_tool() == 'document' && ! $category->get_allow_change())
                return false;
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_parent()->get_course_id());
        $conditions[] = new InCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $category_id);
        $conditions[] = new EqualityCondition('tool', $this->get_parent()->get_tool_id());
        $condition = new AndCondition($conditions);
        
        $count = $wdm->count_content_object_publications($condition);
        return ($count == 0);
    }

    function allowed_to_edit_category($category_id)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        
        $category = $wdm->retrieve_content_object_publication_categories(new EqualityCondition('id', $category_id))->next_result();
        if ($category)
        {
            if ($category->get_tool() == 'document' && ! $category->get_allow_change())
                return false;
        }
        
        return true;
    }

    function count_categories($condition)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        
        if ($condition)
            $conditions[] = $condition;
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $this->get_parent()->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_TOOL, $this->get_parent()->get_tool_id());
        $condition = new AndCondition($conditions);
        
        return $wdm->count_content_object_publication_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $this->get_parent()->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_TOOL, $this->get_parent()->get_tool_id());
        $condition = new AndCondition($conditions);
        
        return $wdm->retrieve_content_object_publication_categories($condition, $offset, $count, $order_property);
    }

    function get_next_category_display_order($parent_id)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $category = $this->get_category();
        $category->set_parent($parent_id);
        
        return $wdm->get_next_content_object_publication_category_display_order($category->get_course(), $category->get_tool(), $parent_id);
    }
}
?>