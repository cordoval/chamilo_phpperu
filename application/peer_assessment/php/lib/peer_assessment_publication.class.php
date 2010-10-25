<?php
/**
 * This class describes a PeerAssessmentPublication data object
 *
 * @author Nick Van Loocke
 */
class PeerAssessmentPublication extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * PeerAssessmentPublication properties
     */
    const PROPERTY_CONTENT_OBJECT = 'content_object_id';
    const PROPERTY_PARENT_ID = 'parent_id';
    const PROPERTY_CATEGORY = 'category_id';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_HIDDEN = 'hidden';
    const PROPERTY_PUBLISHER = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';
    const PROPERTY_MODIFIED = 'modified';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const PROPERTY_CRITERIA_CONTENT_OBJECT_ID = 'criteria_content_object_id';
    
    const PROPERTY_SELECT_USER = 'select_user';
    
    private $target_users;
    private $target_groups;

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CONTENT_OBJECT, self :: PROPERTY_PARENT_ID, self :: PROPERTY_CATEGORY, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_HIDDEN, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED, self :: PROPERTY_MODIFIED, self :: PROPERTY_DISPLAY_ORDER));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PeerAssessmentDataManager :: get_instance();
    }

    /**
     * Returns the content_object of this PeerAssessmentPublication.
     * @return the content_object.
     */
    function get_content_object()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT);
    }

    /**
     * Sets the content_object of this PeerAssessmentPublication.
     * @param content_object
     */
    function set_content_object($content_object)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT, $content_object);
    }

    /**
     * Returns the parent_id of this PeerAssessmentPublication.
     * @return the parent_id.
     */
    function get_parent_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_ID);
    }

    /**
     * Sets the parent_id of this PeerAssessmentPublication.
     * @param parent_id
     */
    function set_parent_id($parent_id)
    {
        $this->set_default_property(self :: PROPERTY_PARENT_ID, $parent_id);
    }

    /**
     * Returns the category of this PeerAssessmentPublication.
     * @return the category.
     */
    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    /**
     * Sets the category of this PeerAssessmentPublication.
     * @param category
     */
    function set_category($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    /**
     * Returns the from_date of this PeerAssessmentPublication.
     * @return the from_date.
     */
    function get_from_date()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }
    
    /**
     * Determines whether this publication is available forever
     * @return boolean True if the publication is available forever
     * @see get_from_date()
     * @see get_to_date()
     */
    function is_forever()
    {
        return $this->get_from_date() == 0 && $this->get_to_date() == 0;
    }

    /**
     * Sets the from_date of this PeerAssessmentPublication.
     * @param from_date
     */
    function set_from_date($from_date)
    {
        $this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
    }

    /**
     * Returns the to_date of this PeerAssessmentPublication.
     * @return the to_date.
     */
    function get_to_date()
    {
        return $this->get_default_property(self :: PROPERTY_TO_DATE);
    }

    /**
     * Sets the to_date of this PeerAssessmentPublication.
     * @param to_date
     */
    function set_to_date($to_date)
    {
        $this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
    }

    /**
     * Returns the hidden of this PeerAssessmentPublication.
     * @return the hidden.
     */
    function get_hidden()
    {
        return $this->get_default_property(self :: PROPERTY_HIDDEN);
    }

    /**
     * Sets the hidden of this PeerAssessmentPublication.
     * @param hidden
     */
    function set_hidden($hidden)
    {
        $this->set_default_property(self :: PROPERTY_HIDDEN, $hidden);
    }

    /**
     * Returns the publisher of this PeerAssessmentPublication.
     * @return the publisher.
     */
    function get_publisher()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER);
    }

    /**
     * Sets the publisher of this PeerAssessmentPublication.
     * @param publisher
     */
    function set_publisher($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    /**
     * Returns the published of this PeerAssessmentPublication.
     * @return the published.
     */
    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    /**
     * Sets the published of this PeerAssessmentPublication.
     * @param published
     */
    function set_published($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }

    /**
     * Returns the modified of this PeerAssessmentPublication.
     * @return the modified.
     */
    function get_modified()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIED);
    }

    /**
     * Sets the modified of this PeerAssessmentPublication.
     * @param modified
     */
    function set_modified($modified)
    {
        $this->set_default_property(self :: PROPERTY_MODIFIED, $modified);
    }

    /**
     * Returns the display_order of this PeerAssessmentPublication.
     * @return the display_order.
     */
    function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Sets the display_order of this PeerAssessmentPublication.
     * @param display_order
     */
    function set_display_order($display_order)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
    }
    

	/**
     * Returns the criteria_content_object_id of this PeerAssessmentPublication.
     * @return the criteria_content_object_id.
     */
    function get_criteria_content_object_id()
    {
        return $this->get_optional_property(self :: PROPERTY_CRITERIA_CONTENT_OBJECT_ID);
    }

    /**
     * Sets the criteria_content_object_id of this PeerAssessmentPublication.
     * @param criteria_content_object_id
     */
    function set_criteria_content_object_id($criteria_content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CRITERIA_CONTENT_OBJECT_ID, $criteria_content_object_id);
    }
    
    /**
     * Sets the target_groups of this PeerAssessmentPublication.
     * @param target_groups
     */
	function set_target_groups($target_groups)
    {
        $this->target_groups = $target_groups;
    }
 
    /**
     * Gets the target_groups of this PeerAssessmentPublication.
     * @param target_groups
     */
	function get_target_groups()
    {
        if (! $this->target_groups)
        {
            $condition = new EqualityCondition(PeerAssessmentPublicationGroup :: PROPERTY_PEER_ASSESSMENT_PUBLICATION, $this->get_id());
            $groups = $this->get_data_manager()->retrieve_peer_assessment_publication_groups($condition);
            
            while ($group = $groups->next_result())
            {
                $this->target_groups[] = $group->get_group_id();
            }
        }
        
        return $this->target_groups;
    }
    
    /**
     * Sets the target_users of this PeerAssessmentPublication.
     * @param target_users
     */
	function set_target_users($target_users)
    {
        $this->target_users = $target_users;
    }

    /**
     * Gets the target_users of this PeerAssessmentPublication.
     * @param target_users
     */
    function get_target_users()
    {
        if (! $this->target_users)
        {
            $condition = new EqualityCondition(PeerAssessmentPublicationUser :: PROPERTY_PEER_ASSESSMENT_PUBLICATION, $this->get_id());
            $users = $this->get_data_manager()->retrieve_peer_assessment_publication_users($condition);
            
            while ($user = $users->next_result())
            {
                $this->target_users[] = $user->get_user();
            }
        }
        
        return $this->target_users;
    }
    
    /**
     * Swtiches the visibility.
     */   
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

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
    
    
	function is_visible_for_target_user($user)
    {
        if ($user->is_platform_admin() || $user->get_id() == $this->get_publisher())
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
                
                while ($user_group = $user_groups->next_result())
                {
                    if (in_array($user_group->get_id(), $this->get_target_groups()))
                    {
                        $allowed = true;
                        break;
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
        
        $time = time();
        
        if ($time < $this->get_from_date() || $time > $this->get_to_date() && !$this->is_forever())
        {
            return false;
        }
        
        return true;
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

}
?>