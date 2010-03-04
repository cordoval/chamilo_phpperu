<?php
/**
 * $Id: description_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.description.component.description_viewer
 */
//require_once dirname(__FILE__).'/../../weblcms_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../content_object_publication.class.php';
require_once dirname(__FILE__) . '/../../../../content_object_publication_browser.class.php';
require_once dirname(__FILE__) . '/../../../../browser/list_renderer/content_object_publication_details_renderer.class.php';
require_once dirname(__FILE__) . '/../../../../browser/list_renderer/list_content_object_publication_list_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/description/description.class.php';
/**
 * This class allows the end user to browse through published descriptions.
 */
class DescriptionBrowser extends ContentObjectPublicationBrowser
{

    /**
     * Constructor
     */
    function DescriptionBrowser($parent, $types)
    {
        parent :: __construct($parent, 'description');
        if (Request :: get('pid') && $parent->get_action() == 'view')
        {
            $this->set_publication_id(Request :: get('pid'));
            $parent->set_parameter(Tool :: PARAM_ACTION, DescriptionTool :: ACTION_VIEW_DESCRIPTIONS);
            $renderer = new ContentObjectPublicationDetailsRenderer($this);
        }
        else
        {
            $renderer = new ListContentObjectPublicationListRenderer($this);
            $actions = array(Tool :: ACTION_DELETE => Translation :: get('DeleteSelected'), Tool :: ACTION_HIDE => Translation :: get('Hide'), Tool :: ACTION_SHOW => Translation :: get('Show'));
            $renderer->set_actions($actions);
        }
        
        $this->set_publication_list_renderer($renderer);
    }

    /*
	 * Inherited.
	 */
    function get_publications($from, $count, $column, $direction)
    {
        $datamanager = WeblcmsDataManager :: get_instance();
        
    	if ($this->is_allowed(EDIT_RIGHT))
        {
            $user_id = array();
            $course_group_ids = array();
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
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'description');
        //$conditions[] = new InCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $category);
        

        $access = array();
        $access[] = new InCondition('user_id', $user_id, $datamanager->get_database()->get_alias('content_object_publication_user'));
        $access[] = new InCondition('course_group_id', $course_group_ids, $datamanager->get_database()->get_alias('content_object_publication_course_group'));
        if (! empty($user_id) || ! empty($course_group_ids))
        {
            $access[] = new AndCondition(array(new EqualityCondition('user_id', null, $datamanager->get_database()->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_database()->get_alias('content_object_publication_course_group'))));
        }
        $conditions[] = new OrCondition($access);
        
        $subselect_conditions = array();
        $subselect_conditions[] = new EqualityCondition('type', 'description');
        if ($this->get_parent()->get_condition())
        {
            $subselect_conditions[] = $this->get_parent()->get_condition();
        }
        $subselect_condition = new AndCondition($subselect_conditions);
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->get_database()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
        $condition = new AndCondition($conditions);
        
        $publications = $datamanager->retrieve_content_object_publications_new($condition, new ObjectTableOrder(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC));
        
        $visible_publications = array();
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

    /*
	 * Inherited.
	 */
    function get_publication_count()
    {
        return count($this->get_publications());
    }
}
?>