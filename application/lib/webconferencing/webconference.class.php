<?php
/**
 * $Id: webconference.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing
 */


require_once dirname(__FILE__) . '/webconference_group.class.php';
require_once dirname(__FILE__) . '/webconference_user.class.php';

/**
 * This class describes a Webconference data object
 *
 * @author Stefaan Vanbillemont, Michael Kyndt
 */
class Webconference extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * Webconference properties
     */
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_CONFKEY = 'confkey';
    const PROPERTY_CONFNAME = 'confname';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_DURATION = 'duration';
    const PROPERTY_HIDDEN = 'hidden';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    
    private $target_groups;
    private $target_users;

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID, self :: PROPERTY_CONFKEY, self :: PROPERTY_CONFNAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_DURATION, self :: PROPERTY_HIDDEN, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WebconferencingDataManager :: get_instance();
    }

    /**
     * Returns the user_id of this Webconference owner.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Sets the user_id of this Webconference ownser.
     * @param user_id
     */
    
    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    /**
     * Returns the confkey of this Webconference.
     * @return the confkey.
     */
    function get_confkey()
    {
        return $this->get_default_property(self :: PROPERTY_CONFKEY);
    }

    /**
     * Sets the confkey of this Webconference.
     * @param confkey
     */
    function set_confkey($confkey)
    {
        $this->set_default_property(self :: PROPERTY_CONFKEY, $confkey);
    }

    /**
     * Returns the confname of this Webconference.
     * @return the confname.
     */
    function get_confname()
    {
        return $this->get_default_property(self :: PROPERTY_CONFNAME);
    }

    /**
     * Sets the confname of this Webconference.
     * @param confname
     */
    function set_confname($confname)
    {
        $this->set_default_property(self :: PROPERTY_CONFNAME, $confname);
    }

    /**
     * Returns the description of this Webconference.
     * @return the conference description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description this Webconference.
     * @param the conference description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the duration of this Webconference.
     * @return the duration.
     */
    function get_duration()
    {
        return $this->get_default_property(self :: PROPERTY_DURATION);
    }

    /**
     * Sets the duration of this Webconference.
     * @param duration
     */
    function set_duration($duration)
    {
        $this->set_default_property(self :: PROPERTY_DURATION, $duration);
    }

    /**
     * Returns the from_date of this Webconference.
     * @return the from_date.
     */
    function get_from_date()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }

    /**
     * Sets the from_date of this Webconference.
     * @param from_date
     */
    function set_from_date($from_date)
    {
        $this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
    }

    /**
     * Returns the to_date of this Webconference.
     * @return the to_date
     */
    function get_to_date()
    {
        return $this->get_default_property(self :: PROPERTY_TO_DATE);
    }

    /**
     * Returns the to_date of this Webconference.
     * @param to_date
     */
    function set_to_date($to_date)
    {
        $this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
    }

    /**
     * Returns the hidden of this Webconference.
     * @return the hidden.
     */
    function get_hidden()
    {
        return $this->get_default_property(self :: PROPERTY_HIDDEN);
    }

    /**
     * Sets the hidden of this Webconference.
     * @param hidden
     */
    function set_hidden($hidden)
    {
        $this->set_default_property(self :: PROPERTY_HIDDEN, $hidden);
    }

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

    function get_target_groups()
    {
        if (! $this->target_groups)
        {
            $condition = new EqualityCondition(WebconferenceGroup :: PROPERTY_WEBCONFERENCE, $this->get_id());
            $groups = $this->get_data_manager()->retrieve_webconference_groups($condition);
            
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
            $condition = new EqualityCondition(WebconferenceUser :: PROPERTY_WEBCONFERENCE, $this->get_id());
            $users = $this->get_data_manager()->retrieve_webconference_users($condition);
            
            while ($user = $users->next_result())
            {
                $this->target_users[] = $user->get_user();
            }
        }
        
        return $this->target_users;
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
        
        if ($time < $this->get_from_date() || $time > $this->get_to_date())
        {
            return false;
        }
        
        return true;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>