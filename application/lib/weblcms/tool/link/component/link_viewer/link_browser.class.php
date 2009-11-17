<?php
/**
 * $Id: link_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.link.component.link_viewer
 */
require_once dirname(__FILE__) . '/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../content_object_publication_browser.class.php';
require_once dirname(__FILE__) . '/../../../../browser/learningobjectpublicationcategorytree.class.php';
require_once dirname(__FILE__) . '/link_publication_list_renderer.class.php';
require_once dirname(__FILE__) . '/link_details_renderer.class.php';
require_once dirname(__FILE__) . '/../../../../browser/list_renderer/content_object_publication_details_renderer.class.php';

class LinkBrowser extends ContentObjectPublicationBrowser
{

    function LinkBrowser($parent, $types)
    {
        parent :: __construct($parent, 'link');
        
        if (Request :: get('pid'))
        {
            $this->set_publication_id(Request :: get('pid'));
            //$renderer = new ContentObjectPublicationDetailsRenderer($this);
            $renderer = new LinkDetailsRenderer($this);
        }
        else
        {
            $tree_id = 'pcattree';
            $tree = new ContentObjectPublicationCategoryTree($this, $tree_id);
            $renderer = new LinkPublicationListRenderer($this);
            $this->set_publication_category_tree($tree);
            $actions = array(Tool :: ACTION_DELETE => Translation :: get('DeleteSelected'), Tool :: ACTION_HIDE => Translation :: get('Hide'), Tool :: ACTION_SHOW => Translation :: get('Show'));
            $renderer->set_actions($actions);
        }
        $this->set_publication_list_renderer($renderer);
    
    }

    function get_allowed_types()
    {
        return $this->get_parent()->get_allowed_types();
    }

    function get_publications($from, $count, $column, $direction)
    {
        $datamanager = WeblcmsDataManager :: get_instance();
        
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $user_id = array();
            $course_groups = array();
        }
        else
        {
            $user_id = $this->get_user_id();
            $course_groups = $this->get_course_groups();
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'link');
        $conditions[] = new InCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $this->get_publication_category_tree()->get_current_category_id());
        
        $access = array();
        $access[] = new InCondition('user_id', $user_id, $datamanager->get_database()->get_alias('content_object_publication_user'));
        $access[] = new InCondition('course_group_id', $course_groups, $datamanager->get_database()->get_alias('content_object_publication_course_group'));
        if (! empty($user_id) || ! empty($course_groups))
        {
            $access[] = new AndCondition(array(new EqualityCondition('user_id', null, $datamanager->get_database()->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_database()->get_alias('content_object_publication_course_group'))));
        }
        $conditions[] = new OrCondition($access);
        
        $subselect_conditions = array();
        $subselect_conditions[] = new EqualityCondition('type', 'link');
        if ($this->get_parent()->get_condition())
        {
            $subselect_conditions[] = $this->get_parent()->get_condition();
        }
        $subselect_condition = new AndCondition($subselect_conditions);
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->get_database()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
        $condition = new AndCondition($conditions);
        
        $publications = $datamanager->retrieve_content_object_publications_new($condition, new ObjectTableOrder(Link :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC));
        
        while ($publication = $publications->next_result())
        {
            // If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
            if (! $publication->is_visible_for_target_users() && ! ($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
            {
                continue;
            }
            $visible_publications[] = $publication;
        }
        return $visible_publications;
    }

    function get_publication_count($category = null)
    {
        if (is_null($category))
        {
            $category = $this->get_publication_category_tree()->get_current_category_id();
        }
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $user_id = array();
            $course_groups = array();
        }
        else
        {
            $user_id = $this->get_user_id();
            $course_groups = $this->get_course_groups();
        }
        
        $dm = WeblcmsDataManager :: get_instance();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'link');
        $conditions[] = new InCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $category);
        
        $access = array();
        $access[] = new InCondition('user_id', $user_id, $dm->get_database()->get_alias('content_object_publication_user'));
        $access[] = new InCondition('course_group_id', $course_groups, $dm->get_database()->get_alias('content_object_publication_course_group'));
        if (! empty($user_id) || ! empty($course_groups))
        {
            $access[] = new AndCondition(array(new EqualityCondition('user_id', null, $dm->get_database()->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $dm->get_database()->get_alias('content_object_publication_course_group'))));
        }
        
        $conditions[] = new OrCondition($access);
        $condition = new AndCondition($conditions);
        
        return $dm->count_content_object_publications_new($condition);
    }
}
?>