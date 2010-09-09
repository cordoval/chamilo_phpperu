<?php
require_once Path :: get_application_path() . 'lib/survey/trackers/survey_participant_tracker.class.php';
require_once Path :: get_application_path() . 'lib/survey/trackers/survey_participant_tracker.class.php';

class SurveyPublication extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication';
    const PARTICIPANT_ROOTCONTEXT = 'ROOT';
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
    //    const PROPERTY_CATEGORY = 'category_id';
    //    const PROPERTY_TEST = 'test';
    

    const TYPE_TEST_CASE = 1;
    const TYPE_NAME_TEST_CASE = 'testcase';
    const TYPE_OFFICIAL = 2;
    const TYPE_NAME_OFFICIAL = 'official';
    const TYPE_VOLUNTEER = 3;
    const TYPE_NAME_VOLUNTEER = 'volunteer';
    
    private $target_groups;
    private $target_users;

    /**
     * Get the default properties
     * @return array The property names.
     */
    
    public function create()
    {
        $succes = parent :: create();
        //        if ($succes)
        //        {
        //            foreach ($this->get_target_user_ids() as $user_id)
        //            {
        //                $this->create_participant_trackers($user_id);
        //            }
        //        
        //        }
        

        $parent_location = SurveyRights :: get_surveys_subtree_root_id();
        return SurveyRights :: create_location_in_surveys_subtree($this->get_content_object(), $this->get_id(), $parent_location, SurveyRights :: TYPE_PUBLICATION);
    
    }

    public function update()
    {
        
        $succes = parent :: update();
        //        if ($succes)
        //        {
        //            
        //            $dummy = new SurveyParticipantTracker();
        //            $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->get_id());
        //            $trackers = $dummy->retrieve_tracker_items($condition);
        //            
        //            $user_ids = $this->get_target_user_ids();
        //            $tracker_user_ids = array();
        //            
        //            foreach ($trackers as $tracker)
        //            {
        //                $user_id = $tracker->get_user_id();
        //                $key = array_search($user_id, $user_ids);
        //                
        //                if ($key == false)
        //                {
        //                    
        //                    $tracker->delete();
        //                }
        //                else
        //                {
        //                    
        //                    $tracker_user_ids[] = $user_id;
        //                }
        //            }
        //            
        //            $new_tracker_user_ids = array_diff($user_ids, $tracker_user_ids);
        //            foreach ($new_tracker_user_ids as $user_id)
        //            {
        //                $this->create_participant_trackers($user_id);
        //            }
        //        
        //        }
        

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
        //        if ($succes)
        //        {
        //            $dummy = new SurveyParticipantTracker();
        //            $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->get_id());
        //            $trackers = $dummy->retrieve_tracker_items($condition);
        //            
        //            foreach ($trackers as $tracker)
        //            {
        //                $tracker->delete();
        //            }
        //        
        //        }
        

        return $succes;
    }

    public function create_participant_trackers($user_id)
    {
        
        $succes = false;
//        $dm = UserDataManager :: get_instance();
//        $user_name = $dm->retrieve_user($user_id)->get_email();
        $survey = $this->get_publication_object();
        //        $survey = RepositoryDataManager :: get_instance()->retrieve_content_object($this->get_content_object());
        

        $context_template = $survey->get_context_template();
        
        //        dump($context_template);
        

        $args = array();
        $args[SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID] = $this->get_id();
        $args[SurveyParticipantTracker :: PROPERTY_USER_ID] = $user_id;
        
        if (! $context_template)
        {
            
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_PARENT_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME] = 'NOCONTEXT';
            $tracker = Event :: trigger(SurveyParticipantTracker :: CREATE_PARTICIPANT_EVENT, SurveyManager::APPLICATION_NAME, $args);
            $succes = true;
            //            dump($tracker);
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
            
//            dump($level_matrix);
            
            $tracker_matrix = array();
            
            $condition = new EqualityCondition(SurveyTemplate :: PROPERTY_USER_ID, $user_id, SurveyTemplate :: get_table_name());
            $templates = SurveyContextDataManager :: get_instance()->retrieve_survey_templates($context_template->get_type(), $condition);
            
            while ($template = $templates->next_result())
            {
                //                $parent_id = 0;
                $property_names = $template->get_additional_property_names(true);
                //                dump($property_names);
                $level = 0;
                $parent_level_context_id = 0;
                
                foreach ($property_names as $property_name => $context_type)
                {
                    
//                    dump($level);
//                    dump($property_name);
//                    dump($context_type);
                    
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
                    //                    dump($context_id);
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
                        $context = SurveyContextDataManager::get_instance()->retrieve_survey_context_by_id($context_id);
                        
                        $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME] = $context->get_name();
                        $tracker = Event :: trigger(SurveyParticipantTracker :: CREATE_PARTICIPANT_EVENT, SurveyManager::APPLICATION_NAME, $args);
                        //                    dump($tracker);
                        //                    $parent_id = $tracker[0]->get_id();
                        $tracker_matrix[$level][$context_id] = $tracker[0]->get_id();
//                        dump($args);
                        $succes = true;
                    }
                    
                    $level ++;
//                    dump($level);
                    //                    dump($parent_id);
                }
                //                exit;
            }
        
        }
        
//        dump($tracker_matrix);
//        
//        exit();
        
        return $succes;
        //        dump($context_template->get_type());
    //        
    //        dump($context_template);
    //        exit();
    //        
    //        $this->create_contexts($user_id, $template, $user_name);
    }

    //    private function create_contexts($user_id, $template, $key, $parent_participant_id = 0)
    //    {
    //        $context_type = $template->get_context_type();
    //        $key_type = $template->get_key();
    //        
    //        $context = SurveyContext :: factory($context_type);
    //        
    //        $contexts = $context->create_contexts_for_user($user_id, $key, $key_type);
    //        
    //        $args = array();
    //        $args[SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID] = $this->get_id();
    //        $args[SurveyParticipantTracker :: PROPERTY_USER_ID] = $user_id;
    //        $args[SurveyParticipantTracker :: PROPERTY_PARENT_ID] = $parent_participant_id;
    //        $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] = $template->get_id();
    //        
    //        foreach ($contexts as $cont)
    //        {
    //            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_ID] = $cont->get_id();
    //            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME] = $cont->get_name();
    //            $tracker = Event :: trigger('survey_participation', 'survey', $args);
    //            
    //            if ($template->has_children())
    //            {
    //                $temps = $template->get_children(false);
    //                while ($temp = $temps->next_result())
    //                {
    //                    $key = $cont->get_additional_property($temp->get_key());
    //                    $this->create_contexts($user_id, $temp, $key, $tracker[0]->get_id());
    //                }
    //            }
    //        }
    //    }
    

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

    //    /**
    //     * Returns the category of this SurveyPublication.
    //     * @return the category.
    //     */
    //    function get_category()
    //    {
    //        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    //    }
    //
    //    /**
    //     * Sets the category of this SurveyPublication.
    //     * @param category
    //     */
    //    function set_category($category)
    //    {
    //        $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    //    }
    

    function set_target_groups($target_groups)
    {
        $this->target_groups = $target_groups;
    }

    function set_target_users($target_users)
    {
        $this->target_users = $target_users;
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

    function is_test()
    {
        return $this->get_default_property(self :: PROPERTY_TEST);
    }

    function get_target_groups()
    {
        if (! $this->target_groups)
        {
            $condition = new EqualityCondition(SurveyPublicationGroup :: PROPERTY_SURVEY_PUBLICATION, $this->get_id());
            $groups = $this->get_data_manager()->retrieve_survey_publication_groups($condition);
            
            while ($group = $groups->next_result())
            {
                $this->target_groups[] = $group->get_group_id();
            }
        }
        
        return $this->target_groups;
    }

    function get_target_users()
    {
        if (! isset($this->target_users))
        {
            $this->target_users = array();
            $condition = new EqualityCondition(SurveyPublicationUser :: PROPERTY_SURVEY_PUBLICATION, $this->get_id());
            $users = $this->get_data_manager()->retrieve_survey_publication_users($condition);
            
            while ($user = $users->next_result())
            {
                $this->target_users[] = $user->get_user();
            }
        }
        return $this->target_users;
    }

    function get_target_user_ids()
    {
        $user_ids = array();
        $groups = $this->get_target_groups();
        
        if (isset($groups) && (count($groups) != 0))
        {
            $gdm = GroupDataManager :: get_instance();
            foreach ($groups as $group_id)
            {
                $group = $gdm->retrieve_group($group_id);
                $user_ids = array_merge($user_ids, $group->get_users(true, true));
            }
        }
        $user_ids = array_merge($user_ids, $this->get_target_users());
        
        return $user_ids;
    }

    function get_user_count()
    {
        
        $user_count = 0;
        $groups = $this->get_target_groups();
        if (isset($groups) && (count($groups) != 0))
        {
            $gdm = GroupDataManager :: get_instance();
            foreach ($groups as $group_id)
            {
                $group = $gdm->retrieve_group($group_id);
                $user_count += $group->count_users(true, true);
            }
        }
        $user_count += count($this->get_target_users());
        return $user_count;
    }

    function is_visible_for_target_user($user, $exclude_publisher = false)
    {
        if ($user->is_platform_admin())
        {
            return true;
        }
        
        if (! $exclude_publisher && $user->get_id() == $this->get_publisher())
        {
            return true;
        }
        
        if ($this->get_target_groups() || $this->get_target_users())
        {
            $allowed = false;
            
            if (in_array($user->get_id(), $this->get_target_users()))
            {
                
                $allowed = true;
            }
            
            if (! $allowed)
            {
                $user_groups = $user->get_groups();
                
                if (isset($user_groups))
                {
                    while ($user_group = $user_groups->next_result())
                    {
                        if (in_array($user_group->get_id(), $this->get_target_groups()))
                        {
                            $allowed = true;
                            break;
                        }
                    }
                }
            
            }
            
            if (! $allowed)
            {
                return false;
            }
        }
        
        if ($this->get_hidden())
        {
            
            return false;
        }
        
        if (! $this->is_publication_period())
        {
            
            return false;
        }
        
        return true;
    }

    function is_publication_period()
    {
        
        $from_date = $this->get_from_date();
        $to_date = $this->get_to_date();
        if ($from_date == 0 && $to_date == 0)
        {
            return true;
        }
        
        $time = time();
        
        if ($time < $from_date || $time > $to_date)
        {
            return false;
        }
        else
        {
            return true;
        }
    
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    function get_publication_object()
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object($this->get_content_object());
    }

    function get_publication_publisher()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($this->get_publisher());
    }

    function count_unique_participants()
    {
        $dummy = new SurveyParticipantTracker();
        $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->get_id());
        $trackers = $dummy->retrieve_tracker_items_result_set($condition);
        $user_ids = array();
        while ($tracker = $trackers->next_result())
        {
            $user_ids[] = $tracker->get_user_id();
        }
        $user_ids = array_unique($user_ids);
        return count($user_ids);
    
    }

    function count_excluded_participants()
    {
        $dummy = new SurveyParticipantTracker();
        $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->get_id());
        $trackers = $dummy->retrieve_tracker_items_result_set($condition);
        $user_ids = array();
        while ($tracker = $trackers->next_result())
        {
            $user_ids[] = $tracker->get_user_id();
        }
        $user_ids = array_unique($user_ids);
        $user_ids = array_diff($this->get_target_user_ids(), $user_ids);
        return count($user_ids);
    
    }

    function get_excluded_participants()
    {
        $dummy = new SurveyParticipantTracker();
        $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->get_id());
        $trackers = $dummy->retrieve_tracker_items_result_set($condition);
        $user_ids = array();
        while ($tracker = $trackers->next_result())
        {
            $user_ids[] = $tracker->get_user_id();
        }
        $user_ids = array_unique($user_ids);
        $user_ids = array_diff($this->get_target_user_ids(), $user_ids);
        return $user_ids;
    
    }

}

?>