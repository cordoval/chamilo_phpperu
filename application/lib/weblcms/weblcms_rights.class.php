<?php
/**
 * $Id: weblcms_rights.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms
 */
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';

class WeblcmsRights
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

    static function is_allowed($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, WeblcmsManager :: APPLICATION_NAME);
    }

    static function get_location_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_by_identifier(WeblcmsManager :: APPLICATION_NAME, $type, $identifier);
    }

    static function get_location_id_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_id_by_identifier(WeblcmsManager :: APPLICATION_NAME, $type, $identifier);
    }

    static function get_root_id()
    {
        return RightsUtilities :: get_root_id(WeblcmsManager :: APPLICATION_NAME);
    }

    static function get_root()
    {
        return RightsUtilities :: get_root(WeblcmsManager :: APPLICATION_NAME);
    }

	static function create_location_in_courses_subtree($name, $type, $identifier, $parent, $tree_identifier = 0)
    {
    	return RightsUtilities :: create_location($name, WeblcmsManager :: APPLICATION_NAME, $type, $identifier, 1, $parent, 0, $tree_identifier, WeblcmsRights :: TREE_TYPE_COURSE);
    }

    static function get_courses_subtree_root($tree_identifier = 0)
    {
    	return RightsUtilities :: get_root(WeblcmsManager :: APPLICATION_NAME, WeblcmsRights :: TREE_TYPE_COURSE, $tree_identifier);
    }

	static function get_courses_subtree_root_id($tree_identifier = 0)
    {
    	return RightsUtilities :: get_root_id(WeblcmsManager :: APPLICATION_NAME, WeblcmsRights :: TREE_TYPE_COURSE, $tree_identifier);
    }

    static function get_location_id_by_identifier_from_courses_subtree($type, $identifier, $tree_identifier = 0)
    {
    	return RightsUtilities :: get_location_id_by_identifier(WeblcmsManager :: APPLICATION_NAME, $type, $identifier, $tree_identifier, WeblcmsRights :: TREE_TYPE_COURSE);
    }
    
	static function get_location_by_identifier_from_courses_subtree($type, $identifier, $tree_identifier = 0)
    {
    	return RightsUtilities :: get_location_by_identifier(WeblcmsManager :: APPLICATION_NAME, $type, $identifier, $tree_identifier, WeblcmsRights :: TREE_TYPE_COURSE);
    }

	static function is_allowed_in_courses_subtree($right, $location, $type, $tree_identifier = 0)
    {
    	 return RightsUtilities :: is_allowed($right, $location, $type, WeblcmsManager :: APPLICATION_NAME, null, $tree_identifier, WeblcmsRights :: TREE_TYPE_COURSE);
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
}
?>