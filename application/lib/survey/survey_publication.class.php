<?php
require_once Path :: get_application_path() . 'lib/survey/trackers/survey_participant_tracker.class.php';
require_once Path :: get_application_path() . 'lib/survey/trackers/survey_participant_tracker.class.php';

class SurveyPublication extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication';
    
    /**
     * SurveyPublication properties
     */
    const PROPERTY_CONTENT_OBJECT = 'content_object_id';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_HIDDEN = 'hidden';
    const PROPERTY_PUBLISHER = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';
    const PROPERTY_TYPE = 'type';
    
    const TYPE_TEST_CASE = 1;
    const TYPE_NAME_TEST_CASE = 'testcase';
    const TYPE_OFFICIAL = 2;
    const TYPE_NAME_OFFICIAL = 'official';
    const TYPE_VOLUNTEER = 3;
    const TYPE_NAME_VOLUNTEER = 'volunteer';

    public function create()
    {
        $succes = parent :: create();
        $parent_location = SurveyRights :: get_surveys_subtree_root_id();
        return SurveyRights :: create_location_in_surveys_subtree($this->get_content_object(), $this->get_id(), $parent_location, SurveyRights :: TYPE_PUBLICATION);
    
    }

    public function update()
    {
        
        $succes = parent :: update();
        return $succes;
    }

    public function delete()
    {
        
        $location = SurveyRights :: get_location_by_identifier_from_surveys_subtree($this->get_id(), SurveyRights :: TYPE_PUBLICATION);
        if ($location)
        {
            if (! $location->remove())
            {
                return false;
            }
        }
        
        $succes = parent :: delete();
        return $succes;
    }

    public function create_participant_trackers($user_id)
    {
        
        $succes = false;
        $survey = $this->get_publication_object();
        
        $context_template = $survey->get_context_template();
        
        $args = array();
        $args[SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID] = $this->get_id();
        $args[SurveyParticipantTracker :: PROPERTY_USER_ID] = $user_id;
        
        if (! $context_template)
        {
            
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_PARENT_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME] = 'NOCONTEXT';
            $tracker = Event :: trigger(SurveyParticipantTracker :: CREATE_PARTICIPANT_EVENT, SurveyManager :: APPLICATION_NAME, $args);
            $succes = true;
        }
        else
        {
            
            $tracker_matrix = array();
            $level_matrix[] = $context_template->get_id();
            $context_template_children = $context_template->get_children(true);
            while ($child_template = $context_template_children->next_result())
            {
                $level_matrix[] = $child_template->get_id();
            }
            $tracker_matrix = array();
            
            $condition = new EqualityCondition(SurveyTemplate :: PROPERTY_USER_ID, $user_id, SurveyTemplate :: get_table_name());
            $templates = SurveyContextDataManager :: get_instance()->retrieve_survey_templates($context_template->get_type(), $condition);
            
            while ($template = $templates->next_result())
            {
                $property_names = $template->get_additional_property_names(true);
                $level = 0;
                $parent_level_context_id = 0;
                
                foreach ($property_names as $property_name => $context_type)
                {
                    $context_template_id = $level_matrix[$level];
                    
                    if ($tracker_matrix[$level - 1][$parent_level_context_id])
                    {
                        $parent_id = $tracker_matrix[$level - 1][$parent_level_context_id];
                    }
                    else
                    {
                        $parent_id = 0;
                    }
                    
                    $args[SurveyParticipantTracker :: PROPERTY_PARENT_ID] = $parent_id;
                    $context_id = $template->get_additional_property($property_name);
                    $parent_level_context_id = $context_id;
                    
                    if ($tracker_matrix[$level][$context_id])
                    {
                        $level ++;
                        continue;
                    }
                    else
                    {
                        
                        $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] = $context_template_id;
                        $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_ID] = $context_id;
                        $context = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($context_id);
                        
                        $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME] = $context->get_name();
                        $tracker = Event :: trigger(SurveyParticipantTracker :: CREATE_PARTICIPANT_EVENT, SurveyManager :: APPLICATION_NAME, $args);
                        $tracker_matrix[$level][$context_id] = $tracker[0]->get_id();
                        $succes = true;
                    }
                    
                    $level ++;
                }
            }
        }
        return $succes;
    }

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CONTENT_OBJECT, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_HIDDEN, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED, self :: PROPERTY_TYPE));
    }

    function get_data_manager()
    {
        return SurveyDataManager :: get_instance();
    }

    /**
     * Returns the content_object of this SurveyPublication.
     * @return the content_object.
     */
    function get_content_object()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT);
    }

    /**
     * Sets the content_object of this SurveyPublication.
     * @param content_object
     */
    function set_content_object($content_object)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT, $content_object);
    }

    /**
     * Returns the from_date of this SurveyPublication.
     * @return the from_date.
     */
    function get_from_date()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }

    /**
     * Sets the from_date of this SurveyPublication.
     * @param from_date
     */
    function set_from_date($from_date)
    {
        $this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
    }

    /**
     * Returns the to_date of this SurveyPublication.
     * @return the to_date.
     */
    function get_to_date()
    {
        return $this->get_default_property(self :: PROPERTY_TO_DATE);
    }

    /**
     * Sets the to_date of this SurveyPublication.
     * @param to_date
     */
    function set_to_date($to_date)
    {
        $this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
    }

    /**
     * Returns the hidden of this SurveyPublication.
     * @return the hidden.
     */
    function get_hidden()
    {
        return $this->get_default_property(self :: PROPERTY_HIDDEN);
    }

    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    static public function get_types()
    {
        return array(self :: TYPE_TEST_CASE => self :: TYPE_NAME_TEST_CASE, self :: TYPE_OFFICIAL => self :: TYPE_NAME_OFFICIAL, self :: TYPE_VOLUNTEER => self :: TYPE_NAME_VOLUNTEER);
    }

    /**
     * Sets the hidden of this SurveyPublication.
     * @param hidden
     */
    function set_hidden($hidden)
    {
        $this->set_default_property(self :: PROPERTY_HIDDEN, $hidden);
    }

    /**
     * Returns the publisher of this SurveyPublication.
     * @return the publisher.
     */
    function get_publisher()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER);
    }

    /**
     * Sets the publisher of this SurveyPublication.
     * @param publisher
     */
    function set_publisher($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    /**
     * Returns the published of this SurveyPublication.
     * @return the published.
     */
    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    /**
     * Sets the published of this SurveyPublication.
     * @param published
     */
    function set_published($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }

    function toggle_visibility()
    {
        $this->set_hidden(! $this->get_hidden());
    }

    /**
     * Determines whether this publication is hidden or not
     * @return boolean True if the publication is hidden.
     */
    function is_hidden()
    {
        return $this->get_default_property(self :: PROPERTY_HIDDEN);
    }
 

//    function is_visible_for_target_user($user, $exclude_publisher = false)
//    {
//        if ($user->is_platform_admin())
//        {
//            return true;
//        }
//        
//        if (! $exclude_publisher && $user->get_id() == $this->get_publisher())
//        {
//            return true;
//        }
//        
//        if ($this->get_target_groups() || $this->get_target_users())
//        {
//            $allowed = false;
//            
//            if (in_array($user->get_id(), $this->get_target_users()))
//            {
//                
//                $allowed = true;
//            }
//            
//            if (! $allowed)
//            {
//                $user_groups = $user->get_groups();
//                
//                if (isset($user_groups))
//                {
//                    while ($user_group = $user_groups->next_result())
//                    {
//                        if (in_array($user_group->get_id(), $this->get_target_groups()))
//                        {
//                            $allowed = true;
//                            break;
//                        }
//                    }
//                }
//            
//            }
//            
//            if (! $allowed)
//            {
//                return false;
//            }
//        }
//        
//        if ($this->get_hidden())
//        {
//            
//            return false;
//        }
//        
//        if (! $this->is_publication_period())
//        {
//            
//            return false;
//        }
//        
//        return true;
//    }
//
//    function is_publication_period()
//    {
//        
//        $from_date = $this->get_from_date();
//        $to_date = $this->get_to_date();
//        if ($from_date == 0 && $to_date == 0)
//        {
//            return true;
//        }
//        
//        $time = time();
//        
//        if ($time < $from_date || $time > $to_date)
//        {
//            return false;
//        }
//        else
//        {
//            return true;
//        }
//    
//    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    function get_publication_object()
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object($this->get_content_object());
    }

}

?>