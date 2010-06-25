<?php

class InternshipOrganizerPublication extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication';
    
    /**
     * InternshipOrganizerPublication properties
     */
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_PUBLISHER_ID = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';
    const PROPERTY_PUBLICATION_TYPE = 'publication_type';
    const PROPERTY_PUBLICATION_PLACE = 'publication_place';
    const PROPERTY_PLACE_ID = 'place_id';
    
    private $target_groups;
    private $target_users;

    /**
     * Get the default properties
     * @return array The property names.
     */
    
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_CONTENT_OBJECT_ID, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_PUBLISHER_ID, self :: PROPERTY_PUBLISHED, self :: PROPERTY_PUBLICATION_TYPE, self :: PROPERTY_PUBLICATION_PLACE, self :: PROPERTY_PLACE_ID));
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    /**
     * Returns the name of this InternshipOrganizerPublication.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this InternshipOrganizerPublication.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the description of this InternshipOrganizerPublication.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this InternshipOrganizerPublication.
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the content_object_id of this InternshipOrganizerPublication.
     * @return the content_object_id.
     */
    function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * Sets the content_object_id of this InternshipOrganizerPublication.
     * @param content_object_id
     */
    function set_content_object($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /**
     * Returns the from_date of this InternshipOrganizerPublication.
     * @return the from_date.
     */
    function get_from_date()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }

    /**
     * Sets the from_date of this InternshipOrganizerPublication.
     * @param from_date
     */
    function set_from_date($from_date)
    {
        $this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
    }

    /**
     * Returns the to_date of this InternshipOrganizerPublication.
     * @return the to_date.
     */
    function get_to_date()
    {
        return $this->get_default_property(self :: PROPERTY_TO_DATE);
    }

    /**
     * Sets the to_date of this InternshipOrganizerPublication.
     * @param to_date
     */
    function set_to_date($to_date)
    {
        $this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
    }

    /**
     * Returns the publisher_id of this InternshipOrganizerPublication.
     * @return the publisher_id.
     */
    function get_publisher_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER_ID);
    }

    /**
     * Sets the publisher_id of this InternshipOrganizerPublication.
     * @param publisher_id
     */
    function set_publisher_id($publisher_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER_ID, $publisher_id);
    }

    /**
     * Returns the publication_type of this InternshipOrganizerPublication.
     * @return the publication_type.
     */
    function get_publication_type()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_TYPE);
    }

    /**
     * Sets the publication_type of this InternshipOrganizerPublication.
     * @param publication_type
     */
    function set_publication_type($publication_type)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_TYPE, $publication_type);
    }

    /**
     * Returns the publication_place of this InternshipOrganizerPublication.
     * @return the publication_place.
     */
    function get_publication_place()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_PLACE);
    }

    /**
     * Sets the publication_place of this InternshipOrganizerPublication.
     * @param publication_place
     */
    function set_publication_place($publication_place)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_PLACE, $publication_place);
    }

    /**
     * Returns the place_id of this InternshipOrganizerPublication.
     * @return the place_id.
     */
    function get_place_id()
    {
        return $this->get_default_property(self :: PROPERTY_PLACE_ID);
    }

    /**
     * Sets the place_id of this InternshipOrganizerPublication.
     * @param place_id
     */
    function set_place_id($place_id)
    {
        $this->set_default_property(self :: PROPERTY_PLACE_ID, $place_id);
    }

    /**
     * Returns the published of this InternshipOrganizerPublication.
     * @return the published.
     */
    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    /**
     * Sets the published of this InternshipOrganizerPublication.
     * @param published
     */
    function set_published($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }

    function set_target_groups($target_groups)
    {
        $this->target_groups = $target_groups;
    }

    function set_target_users($target_users)
    {
        $this->target_users = $target_users;
    }

    function get_target_groups()
    {
        if (! $this->target_groups)
        {
            $condition = new EqualityCondition(InternshipOrganizerPublicationGroup :: PROPERTY_PUBLICATION_ID, $this->get_id());
            $groups = $this->get_data_manager()->retrieve_publication_groups($condition);
            
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
            $condition = new EqualityCondition(InternshipOrganizerPublicationUser :: PROPERTY_PUBLICATION_ID, $this->get_id());
            $users = $this->get_data_manager()->retrieve_publication_users($condition);
            
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

    function get_content_object()
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object($this->get_content_object_id());
    }

    function get_publication_publisher()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($this->get_publisher());
    }

}

?>