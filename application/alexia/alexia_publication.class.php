<?php
/**
 * $Id: alexia_publication.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia
 */
require_once dirname(__FILE__) . '/alexia_publication_user.class.php';
require_once dirname(__FILE__) . '/alexia_publication_group.class.php';

/**
 * This class describes an AlexiaPublication data object
 *
 * @author Hans De Bisschop
 */
class AlexiaPublication extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication';
    
    /**
     * AlexiaPublication properties
     */
    const PROPERTY_CONTENT_OBJECT = 'content_object_id';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_HIDDEN = 'hidden';
    const PROPERTY_PUBLISHER = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';
    
    private $target_groups;
    private $target_users;

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CONTENT_OBJECT, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_HIDDEN, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return AlexiaDataManager :: get_instance();
    }

    /**
     * Returns the content_object of this AlexiaPublication.
     * @return the content_object.
     */
    function get_content_object()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT);
    }

    /**
     * Sets the content_object of this AlexiaPublication.
     * @param content_object
     */
    function set_content_object($content_object)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT, $content_object);
    }

    /**
     * Returns the from_date of this AlexiaPublication.
     * @return the from_date.
     */
    function get_from_date()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }

    /**
     * Sets the from_date of this AlexiaPublication.
     * @param from_date
     */
    function set_from_date($from_date)
    {
        $this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
    }

    /**
     * Returns the to_date of this AlexiaPublication.
     * @return the to_date.
     */
    function get_to_date()
    {
        return $this->get_default_property(self :: PROPERTY_TO_DATE);
    }

    /**
     * Sets the to_date of this AlexiaPublication.
     * @param to_date
     */
    function set_to_date($to_date)
    {
        $this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
    }

    /**
     * Returns the hidden of this AlexiaPublication.
     * @return the hidden.
     */
    function get_hidden()
    {
        return $this->get_default_property(self :: PROPERTY_HIDDEN);
    }

    /**
     * Determines whether this publication is hidden or not
     * @return boolean True if the publication is hidden.
     */
    function is_hidden()
    {
        return $this->get_default_property(self :: PROPERTY_HIDDEN);
    }

    /**
     * Sets the hidden of this AlexiaPublication.
     * @param hidden
     */
    function set_hidden($hidden)
    {
        $this->set_default_property(self :: PROPERTY_HIDDEN, $hidden);
    }

    /**
     * Returns the publisher of this AlexiaPublication.
     * @return the publisher.
     */
    function get_publisher()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER);
    }

    /**
     * Sets the publisher of this AlexiaPublication.
     * @param publisher
     */
    function set_publisher($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    /**
     * Returns the published of this AlexiaPublication.
     * @return the published.
     */
    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    /**
     * Sets the published of this AlexiaPublication.
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
            $condition = new EqualityCondition(AlexiaPublicationGroup :: PROPERTY_PUBLICATION, $this->get_id());
            $groups = AlexiaDataManager :: get_instance()->retrieve_alexia_publication_groups($condition);
            
            while ($group = $groups->next_result())
            {
                $this->target_groups[] = $group->get_group_id();
            }
        }
        
        return $this->target_groups;
    }

    function get_target_users()
    {
        if (! $this->target_users)
        {
            $condition = new EqualityCondition(AlexiaPublicationUser :: PROPERTY_PUBLICATION, $this->get_id());
            $users = AlexiaDataManager :: get_instance()->retrieve_alexia_publication_users($condition);
            
            while ($user = $users->next_result())
            {
                $this->target_users[] = $user->get_user();
            }
        }
        
        return $this->target_users;
    }

    function is_visible_for_target_user($user_id)
    {
        $user = UserDataManager :: get_instance()->retrieve_user($user_id);
        
        if ($user->is_platform_admin() || $user_id == $this->get_publisher())
            return true;
        
        if ($this->get_target_groups() || $this->get_target_users())
        {
            $allowed = false;
            
            if (in_array($user_id, $this->get_target_users()))
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
        
        if ($time < $this->get_from_date() || $time > $this->get_to_date())
        {
            return false;
        }
        
        return true;
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
}
?>