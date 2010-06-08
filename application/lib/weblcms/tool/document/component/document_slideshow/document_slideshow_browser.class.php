<?php
/**
 * $Id: document_slideshow_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document.component.document_slideshow
 */
require_once dirname(__FILE__) . '/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../content_object_publication_browser.class.php';
require_once dirname(__FILE__) . '/../../../../browser/learningobjectpublicationcategorytree.class.php';
require_once dirname(__FILE__) . '/document_publication_slideshow_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/document/document.class.php';

class DocumentSlideshowBrowser extends ContentObjectPublicationBrowser
{

    function DocumentSlideshowBrowser($parent, $types)
    {
        parent :: __construct($parent, Document :: get_type_name());
        $tree_id = 'pcattree';
        //$tree = new ContentObjectPublicationCategoryTree($this, $tree_id);
        $parent->set_parameter($tree_id, Request :: get($tree_id));
        $renderer = new DocumentPublicationSlideshowRenderer($this);
        $this->set_publication_list_renderer($renderer);
        //$this->set_publication_category_tree($tree);
        $this->set_category(Request :: get($tree_id));
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
                
            $course_group_ids = array();
                
            foreach($course_groups as $course_group)
            {
              	$course_group_ids[] = $course_group->get_id();
            }
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = $this->get_condition($this->get_category());
        
        /*$access = array();
        $access[] = new InCondition('user_id', $user_id, $datamanager->get_alias('content_object_publication_user'));
        $access[] = new InCondition('course_group_id', $course_group_ids, $datamanager->get_alias('content_object_publication_course_group'));
        if (! empty($user_id) || ! empty($course_group_ids))
        {
            $access[] = new AndCondition(array(new EqualityCondition('user_id', null, $datamanager->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_alias('content_object_publication_course_group'))));
        }
        $conditions[] = new OrCondition($access);*/
        
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
        
        $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Document :: get_type_name());
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
        $condition = new AndCondition($conditions);
        
        $publications = $datamanager->retrieve_content_object_publications_new($condition, new ObjectTableOrder(Document :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC));
        $visible_publications = array();
        while ($publication = $publications->next_result())
        {
            // If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
            if (! $publication->is_visible_for_target_users() && ! ($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
            {
                continue;
            }
            $document = $publication->get_content_object();
            if ($document->is_image())
            {
                $visible_publications[] = $publication;
            }
        
        }
        return $visible_publications;
    }

    function get_publication_count($category = null)
    {
        if (is_null($category))
        {
            $category = $this->get_category();
        }
        
        $dm = WeblcmsDataManager :: get_instance();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = $this->get_condition($category);
        
    	$user_id = $this->get_user_id();
        $course_groups = $this->get_course_groups();
                
        $course_group_ids = array();
               
        foreach($course_groups as $course_group)
        {
           	$course_group_ids[] = $course_group->get_id();
        }
        
        /*$access = array();
        $access[] = new InCondition('user_id', $user_id, $dm->get_alias('content_object_publication_user'));
        $access[] = new InCondition('course_group_id', $course_group_ids, $dm->get_alias('content_object_publication_course_group'));
        if (! empty($user_id) || ! empty($course_group_ids))
        {
            $access[] = new AndCondition(array(new EqualityCondition('user_id', null, $dm->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $dm->get_alias('content_object_publication_course_group'))));
        }
        
        $conditions[] = new OrCondition($access);*/
        
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
        
        $condition = new AndCondition($conditions);
        
        return $dm->count_content_object_publications_new($condition);
    }

    function get_condition($category = 0)
    {
        $tool_cond = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'document');
        $category_cond = new EqualityCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $category);
        return new AndCondition($tool_cond, $category_cond);
    }

    function get_category()
    {
        $cat = Request :: get('pcattree');
        return $cat ? $cat : 0;
    }
}
?>