<?php
/**
 * $Id: weblcms_rights.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms
 */
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';

class WeblcmsRights extends RightsUtilities
{
    const VIEW_RIGHT = '1';
    const ADD_RIGHT = '2';
    const EDIT_RIGHT = '3';
    const DELETE_RIGHT = '4';

    const LOCATION_BROWSER = 1;
	const LOCATION_HOME = 2;
	const LOCATION_VIEWER = 3;

	const TREE_TYPE_COURSE = 1;
	const TYPE_CATEGORY = 1;
	const TYPE_COURSE = 2;
	const TYPE_COURSE_MODULE = 3;
	const TYPE_COURSE_CATEGORY = 4;
	const TYPE_PUBLICATION = 5;

	static function get_available_rights()
    {
        $reflect = new ReflectionClass(__CLASS__);

	    $rights = $reflect->getConstants();

	    foreach($rights as $key => $right)
		{
			if(substr(strtolower($key), -5) != 'right')
			{
				unset($rights[$key]);
			}
		}

	    return $rights;
    }

    static function get_location_by_identifier($type, $identifier)
    {
        return parent :: get_location_by_identifier(WeblcmsManager :: APPLICATION_NAME, $type, $identifier);
    }

    static function get_location_id_by_identifier($type, $identifier)
    {
        return parent :: get_location_id_by_identifier(WeblcmsManager :: APPLICATION_NAME, $type, $identifier);
    }

    static function get_root_id()
    {
        return parent :: get_root_id(WeblcmsManager :: APPLICATION_NAME);
    }

    static function get_root()
    {
        return parent :: get_root(WeblcmsManager :: APPLICATION_NAME);
    }

	static function create_location_in_courses_subtree($name, $type, $identifier, $parent, $tree_identifier = 0)
    {
    	return parent :: create_location($name, WeblcmsManager :: APPLICATION_NAME, $type, $identifier, 1, $parent, 0, $tree_identifier, WeblcmsRights :: TREE_TYPE_COURSE);
    }

    static function get_courses_subtree_root($tree_identifier = 0)
    {
    	return parent :: get_root(WeblcmsManager :: APPLICATION_NAME, WeblcmsRights :: TREE_TYPE_COURSE, $tree_identifier);
    }

	static function get_courses_subtree_root_id($tree_identifier = 0)
    {
    	return parent :: get_root_id(WeblcmsManager :: APPLICATION_NAME, WeblcmsRights :: TREE_TYPE_COURSE, $tree_identifier);
    }

    static function get_location_id_by_identifier_from_courses_subtree($type, $identifier, $tree_identifier = 0)
    {
    	return parent :: get_location_id_by_identifier(WeblcmsManager :: APPLICATION_NAME, $type, $identifier, $tree_identifier, WeblcmsRights :: TREE_TYPE_COURSE);
    }
    
	static function get_location_by_identifier_from_courses_subtree($type, $identifier, $tree_identifier = 0)
    {
    	return parent :: get_location_by_identifier(WeblcmsManager :: APPLICATION_NAME, $type, $identifier, $tree_identifier, WeblcmsRights :: TREE_TYPE_COURSE);
    }

	static function is_allowed_in_courses_subtree($right, $location, $type, $tree_identifier = 0)
    {
    	 return self :: is_allowed($right, $location, $type, null, $tree_identifier, WeblcmsRights :: TREE_TYPE_COURSE);
    }
    
    /**
     * Functions used for course_group_right_location
     */
    
	static function invert_course_group_right_location($right, $course_group, $location)
    {
        if (! empty($course_group) && ! empty($right) && ! empty($location))
        {
            $wdm = WeblcmsDataManager :: get_instance();
            $course_group_right_location = $wdm->retrieve_course_group_right_location($right, $course_group, $location);

            if ($course_group_right_location)
            {
                if ($course_group_right_location->is_enabled())
                {
                    return $course_group_right_location->delete();
                }
                else
                {
                    $course_group_right_location->invert();
                    return $course_group_right_location->update();
                }
            }
            else
            {
                $course_group_right_location = new CourseGroupRightLocation();
                $course_group_right_location->set_location_id($location);
                $course_group_right_location->set_right_id($right);
                $course_group_right_location->set_course_group_id($course_group);
                $course_group_right_location->set_value(1);
                return $course_group_right_location->create();
            }
        }
        else
        {
            return false;
        }
    }
    
	static function set_course_group_right_location_value($right, $course_group, $location, $value)
    {
        if (! empty($course_group) && ! empty($right) && ! empty($location) && isset($value))
        {
            $wdm = WeblcmsDataManager :: get_instance();
            $course_group_right_location = $wdm->retrieve_course_group_right_location($right, $course_group, $location);

            if ($course_group_right_location)
            {
                if ($value == true)
                {
                    $course_group_right_location->set_value($value);
                    return $course_group_right_location->update();
                }
                else
                {
                    return $course_group_right_location->delete();
                }
            }
            else
            {
                if ($value == true)
                {
                    $course_group_right_location = new CourseGroupRightLocation();
                    $course_group_right_location->set_location_id($location);
                    $course_group_right_location->set_right_id($right);
                    $course_group_right_location->set_course_group_id($course_group);
                    $course_group_right_location->set_value($value);
                    return $course_group_right_location->create();
                }
                else
                {
                    return true;
                }
            }
        }
        else
        {
            return false;
        }
    }
    
	static function get_course_group_right_location($right_id, $course_group_id, $location_id)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $object = $wdm->retrieve_course_group_right_location($right_id, $course_group_id, $location_id);

        if ($object instanceof CourseGroupRightLocation)
        {
            return $object->get_value();
        }
        else
        {
            return 0;
        }
    }
    
	static function is_allowed_for_course_group($course_group, $right, $location)
    {
        $parents = $location->get_parents();

        while ($parent = $parents->next_result())
        {
            $has_right = self :: get_course_group_right_location($right, $course_group, $parent->get_id());

            if ($has_right)
            {
                return true;
            }

            if (! $parent->inherits())
            {
                return false;
            }
        }

        return false;
    }
    
	static function get_course_group_rights_icon($location_url, $rights_url, $locked_parent, $right, $object, $location)
    {
        $html[] = '<div id="r_' . $right . '_' . $object->get_id() . '_' . $location->get_id() . '" style="float: left; width: 24%; text-align: center;">';
        if (isset($locked_parent))
        {
            $value = self :: get_course_group_right_location($right, $object->get_id(), $locked_parent->get_id());
            $html[] = '<a href="' . $location_url . '">' . ($value == 1 ? '<img src="' . Theme :: get_common_image_path() . 'action_setting_true_locked.png" title="' . Translation :: get('LockedTrue') . '" />' : '<img src="' . Theme :: get_common_image_path() . 'action_setting_false_locked.png" title="' . Translation :: get('LockedFalse') . '" />') . '</a>';
        }
        else
        {
            $value = self :: get_course_group_right_location($right, $object->get_id(), $location->get_id());

            if (! $value)
            {
                if ($location->inherits())
                {
                    $inherited_value = self :: is_allowed_for_course_group($object->get_id(), $right, $location);

                    if ($inherited_value)
                    {
                        $html[] = '<a class="setRight" href="' . $rights_url . '">' . '<div class="rightInheritTrue"></div></a>';
                    }
                    else
                    {
                        $html[] = '<a class="setRight" href="' . $rights_url . '">' . '<div class="rightFalse"></div></a>';
                    }
                }
                else
                {
                    $html[] = '<a class="setRight" href="' . $rights_url . '">' . '<div class="rightFalse"></div></a>';
                }
            }
            else
            {
                $html[] = '<a class="setRight" href="' . $rights_url . '">' . '<div class="rightTrue"></div></a>';
            }
        }
        $html[] = '</div>';

        return implode("\n", $html);
    }
    
    // Rewrite of is_allowed and get_right to check for course groups as well
    
	static function is_allowed($right, $location = 0, $type = self :: TYPE_ROOT, $user_id = null, $tree_identifier = 0, $tree_type = self :: TREE_TYPE_ROOT)
    {
    	// Determine the user_id of the user we're checking a right for
        $udm = UserDataManager :: get_instance();
        $user_id = $user_id ? $user_id : Session :: get_user_id();
        $user = $udm->retrieve_user($user_id);

        $cache_id = md5(serialize(array($right, $location, $type, WeblcmsManager :: APPLICATION_NAME, $user_id, $tree_identifier, $tree_type)));

        if (! isset(self :: $is_allowed_cache[$cache_id]))
        {
            self :: $is_allowed_cache[$cache_id] = self :: get_right($right, $location, $type, $user, $tree_identifier, $tree_type);
        }

        return self :: $is_allowed_cache[$cache_id];
    }
    
	/**
     * @param int $right
     * @param int $location
     * @param string $type
     * @param string $application
     * @param User $user
     * @param int $tree_identifier
     * @param string $tree_type
     * @return boolean
     */
    static function get_right($right, $location, $type, $user, $tree_identifier, $tree_type)
    {
    	$application = WeblcmsManager :: APPLICATION_NAME;

    	if($tree_type != self :: TREE_TYPE_COURSE || $tree_identifier == 0)
    	{
    		return parent :: get_right($right, $location, $type, $application, $user, $tree_identifier, $tree_type);
    	}

    	if ($user instanceof User && $user->is_platform_admin())
        {
            return true;
        }

    	$location = parent :: get_location_by_identifier($application, $type, $location, $tree_identifier, $tree_type);
        if(!$location)
        {
        	return false;
        }

        $locked_parent = $location->get_locked_parent();
        if(isset($locked_parent))
        {
        	$location = $locked_parent;
        }

        if (isset($user))
        {
            // Check right for the user's groups
            //$user_groups = $user->get_groups();
			$course_groups = WeblcmsDataManager :: get_user_course_groups($user, WeblcmsDataManager :: get_instance()->retrieve_course($tree_identifier));
            foreach($course_groups as $course_group_id => $course_group)
            {
                if(self :: is_allowed_for_course_group($course_group_id, $right, $location))
                {
                    return true;
                }
            }

            // Check right for the individual user's configured templates
            $user_templates = $user->get_rights_templates();

            while ($user_template = $user_templates->next_result())
            {
                if(self :: is_allowed_for_rights_template($user_template->get_id(), $right, $location))
                {
                	return true;
                }
            }

            if(self :: is_allowed_for_user($user->get_id(), $right, $location))
            {
            	return true;
            }
        }
        else
        {
            // TODO: Use anonymous user for this, he may or may not have some rights too
            return false;
        }

        return false;
    }
}
?>