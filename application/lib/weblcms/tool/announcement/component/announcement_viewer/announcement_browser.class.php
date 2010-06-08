<?php
/**
 * $Id: announcement_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.announcement.component.announcement_viewer
 */
require_once dirname(__FILE__) . '/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../content_object_publication_browser.class.php';
require_once dirname(__FILE__) . '/../../../../browser/list_renderer/content_object_publication_details_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/announcement/announcement.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/description/description.class.php';
/**
 * Browser to allow the user to view the published announcements
 */
class AnnouncementBrowser extends ContentObjectPublicationBrowser
{
    /**
     * @see ContentObjectPublicationBrowser::ContentObjectPublicationBrowser()
     */
    private $publications;

    function AnnouncementBrowser($parent)
    {
        parent :: __construct($parent, Announcement :: get_type_name());
        if (Request :: get(Tool :: PARAM_PUBLICATION_ID) && $parent->get_action() == 'view')
        {
            $this->set_publication_id(Request :: get(Tool :: PARAM_PUBLICATION_ID));
            $parent->set_parameter(Tool :: PARAM_ACTION, AnnouncementTool :: ACTION_VIEW_ANNOUNCEMENTS);
            $renderer = new ContentObjectPublicationDetailsRenderer($this);
        }
        else
        {
            $renderer = new ListContentObjectPublicationListRenderer($this);
            //$actions = array(Tool :: ACTION_DELETE => Translation :: get('DeleteSelected'), Tool :: ACTION_HIDE => Translation :: get('Hide'), Tool :: ACTION_SHOW => Translation :: get('Show'));
            
            $actions[] = new ObjectTableFormAction(Tool :: ACTION_DELETE, Translation :: get('DeleteSelected'));
        	$actions[] = new ObjectTableFormAction(Tool :: ACTION_HIDE, Translation :: get('Hide'), false);
        	$actions[] = new ObjectTableFormAction(Tool :: ACTION_SHOW, Translation :: get('Show'), false);
            
            $renderer->set_actions($actions);
        }
        
        $this->set_publication_list_renderer($renderer);
    }

    /**
     * Retrieves the publications
     * @return array An array of ContentObjectPublication objects
     */
    function get_publications($from, $count, $column, $direction)
    {
        if (empty($this->publications))
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
            $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'announcement');
            
           /* $access = array();
            $access[] = new InCondition('user_id', $user_id, $datamanager->get_alias('content_object_publication_user'));
            $access[] = new InCondition('course_group_id', $course_group_ids, $datamanager->get_alias('content_object_publication_course_group'));
            if (! empty($user_id) || ! empty($course_groups))
            {
                $access[] = new AndCondition(array(new EqualityCondition('user_id', null, $datamanager->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_alias('content_object_publication_course_group'))));
            }*/
            
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
           
            $subselect_conditions = array();
            $subselect_conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Announcement :: get_type_name());
            if ($this->get_parent()->get_condition())
            {
                $subselect_conditions[] = $this->get_parent()->get_condition();
            }
            $subselect_condition = new AndCondition($subselect_conditions);
            
            $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
            
            $filter = Request :: get(AnnouncementToolViewerComponent :: PARAM_FILTER);
            switch($filter)
            {
            	case AnnouncementToolViewerComponent :: FILTER_TODAY:
            		$time = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
            		$conditions[] = new InequalityCondition(ContentObjectPublication :: PROPERTY_MODIFIED_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $time);
            		break;
            	case AnnouncementToolViewerComponent :: FILTER_THIS_WEEK:
            		$time = strtotime('Next Monday', strtotime('-1 Week', time()));
            		$conditions[] = new InequalityCondition(ContentObjectPublication :: PROPERTY_MODIFIED_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $time);
            		break;
            	case AnnouncementToolViewerComponent :: FILTER_THIS_MONTH:
            		$time = mktime(0, 0, 0, date('m', time()), 1, date('Y', time()));
            		$conditions[] = new InequalityCondition(ContentObjectPublication :: PROPERTY_MODIFIED_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $time);
            		break;	
            }
            
            $condition = new AndCondition($conditions);
            
            $publications = $datamanager->retrieve_content_object_publications_new($condition, new ObjectTableOrder(Announcement :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_ASC));
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
            $this->publications = $visible_publications;
        }
        
        return $this->publications;
    
    }

    /**
     * Retrieves the number of published annoucements
     * @return int
     */
    function get_publication_count()
    {
        return count($this->get_publications());
    }
}
?>