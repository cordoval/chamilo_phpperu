<?php
/**
 * $Id: rights_utilities.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib
 * @author Hans de Bisschop
 */
//require_once 'Tree/Tree.php';
require_once 'XML/Unserializer.php';

/*
 * This should become the class which all applications use
 * to retrieve and add rights. This class should NOT be used by the
 * RightsManager itself. Its is meant to be be used as an interface
 * to the RightsManager / RightsDataManager functionality.
 */

class RightsUtilities
{   
	static function create_location($name, $application, $type = 'root', $identifier = 0, $inherit = 0, $parent = 0, $locked = 0, $tree_identifier = 0, $tree_type='root', $return_location = false)
    {
        $location = new Location();
        $location->set_location($name);
        $location->set_parent($parent);
        $location->set_application($application);
        $location->set_type($type);
        $location->set_identifier($identifier);
        $location->set_inherit($inherit);
        $location->set_locked($locked);
        $location->set_tree_identifier($tree_identifier);
        $location->set_tree_type($tree_type);
        
        $succes = $location->create();
        
        if($return_location && $succes)
        {
        	return $location;
        }
        else
        {
        	return $succes;
        }
    }
    
	static function create_application_root_location($application)
    {
        $xml = self :: parse_locations_file($application);

        $root = self :: create_location($xml['name'], $application, $xml['type'], $xml['identifier'], 0, 0, 0, 0, 'root', true);
        if (!$root)
        {
            return false;
        }

        if (isset($xml['children']) && isset($xml['children']['location']) && count($xml['children']['location']) > 0)
        {
            self :: parse_tree($application, $xml, $root->get_id());
        }

        return true;
    }
    
    static function create_subtree_root_location($application, $tree_identifier, $tree_type)
    {
    	return self :: create_location($tree_type, $application, 'root', 0, 0, 0, 0, $tree_identifier, $tree_type);	
    }

    function parse_locations_file($application)
    {
        $base_path = (WebApplication :: is_application($application) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
        $file = $base_path . $application . '/rights/' . $application . '_locations.xml';

        if (file_exists($file))
        {
            $unserializer = new XML_Unserializer();
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('location'));

            // userialize the document
            $status = $unserializer->unserialize($file, true);

            if (PEAR :: isError($status))
            {
                echo 'Error: ' . $status->getMessage();
            }
            else
            {
                $data = $unserializer->getUnserializedData();
            }
        }

        return $data;
    }

    function parse_tree($application, $xml, $parent)
    {
        $previous = null;

        $children = $xml['children'];
        foreach ($children['location'] as $child)
        {
            $location = new Location();
            $location->set_location($child['name']);
            $location->set_application($application);
            $location->set_type($child['type']);
            
            // TODO: Temporary fix !
            if (is_string($child['identifier']))
            {
                $location->set_identifier(0);
            }
            else
            {
                $location->set_identifier($child['identifier']);
            }
            
            $location->set_parent($parent);
            $location->set_tree_type('root');
            $location->set_tree_identifier(0);
            
            if (! $location->create($previous != null ? $previous : 0))
            {
                return false;
            }

            $previous = $location->get_id();

            if (isset($child['children']) && isset($child['children']['location']) && count($child['children']['location']) > 0)
            {
                self :: parse_tree($application, $child, $location->get_id());
            }
        }
    }

    function is_allowed($right, $location = 0, $type = 'root', $application = 'admin', $user_id = null, $tree_identifier = 0, $tree_type = 'root')
    {
        $rdm = RightsDataManager :: get_instance();
        $udm = UserDataManager :: get_instance();

        $user_id = $user_id ? $user_id : Session :: get_user_id();
        $user = $udm->retrieve_user($user_id);

        if (is_object($user) && $user->is_platform_admin())
        {
            return true;
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(Location :: PROPERTY_IDENTIFIER, $location);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_TYPE, $type);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $application);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_TREE_TYPE, $tree_type);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_TREE_IDENTIFIER, $tree_identifier);

        $condition = new AndCondition($conditions);

        $location_set = $rdm->retrieve_locations($condition, 0, 1);

        if ($location_set->size() > 0)
        {
            $location = $location_set->next_result();
            $locked_parent = $location->get_locked_parent();

            if (isset($locked_parent))
            {
                $location = $locked_parent;
            }

            $parents = $location->get_parents();
        }
        else
        {
            return false;
        }

        $parents = $parents->as_array();

        if (isset($user))
        {

            // Check right for the user's groups
            $user_groups = $user->get_groups();

            if (! is_null($user_groups))
            {
                while ($group = $user_groups->next_result())
                {
                    foreach ($parents as $parent)
                    {
                        $conditions = array();
                        $conditions[] = new EqualityCondition(GroupRightLocation :: PROPERTY_LOCATION_ID, $parent->get_id());
                        $conditions[] = new EqualityCondition(GroupRightLocation :: PROPERTY_RIGHT_ID, $right);
                        $conditions[] = new EqualityCondition(GroupRightLocation :: PROPERTY_GROUP_ID, $group->get_id());
                        $condition = new AndCondition($conditions);

                        $rights = $rdm->retrieve_group_right_locations($condition, null, 1);

                        if ($rights->size() > 0)
                        {
                            $has_right = $rights->next_result()->get_value();

                            if ($has_right)
                            {
                                return true;
                            }
                        }

                        if (! $parent->inherits())
                        {
                            break;
                        }
                    }
                }
            }

            // Check right for the individual user
            foreach ($parents as $parent)
            {
                $conditions = array();
                $conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_LOCATION_ID, $parent->get_id());
                $conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_RIGHT_ID, $right);
                $conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_USER_ID, $user->get_id());
                $condition = new AndCondition($conditions);

                $rights = $rdm->retrieve_user_right_locations($condition, null, 1);

                if ($rights->size() > 0)
                {
                    $has_right = $rights->next_result()->get_value();

                    if ($has_right)
                    {
                        return true;
                    }
                }

                if (! $parent->inherits())
                {
                    break;
                }
            }
        }
        else
        {
            // TODO: Use anonymous user for this, he may or may not have some rights too
            return false;
        }

        return false;
    }

    function is_allowed_for_rights_template($rights_template, $right, $location)
    {
        $rdm = RightsDataManager :: get_instance();

        $parents = $location->get_parents();

        while ($parent = $parents->next_result())
        {
            $has_right = $rdm->retrieve_rights_template_right_location($right, $rights_template, $parent->get_id())->get_value();

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

    function is_allowed_for_user($user, $right, $location)
    {
        $parents = $location->get_parents();

        while ($parent = $parents->next_result())
        {
            $has_right = self :: get_user_right_location($right, $user, $parent->get_id());

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

    function is_allowed_for_group($group, $right, $location)
    {
        $parents = $location->get_parents();

        while ($parent = $parents->next_result())
        {
            $has_right = self :: get_group_right_location($right, $group, $parent->get_id());

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

    function move_multiple($locations, $new_parent_id, $new_previous_id = 0)
    {
        $rdm = RightsDataManager :: get_instance();

        if (! is_array($locations))
        {
            $locations = array($locations);
        }

        $failures = 0;

        foreach ($locations as $location)
        {
            if (! $rdm->move_location_nodes($location, $new_parent_id, $new_previous_id))
            {
                $failures ++;
            }
        }
    }

    function get_root($application, $tree_type = 'root', $tree_identifier = 0)
    {
        $rdm = RightsDataManager :: get_instance();

        $root_conditions = array();
        $root_conditions[] = new EqualityCondition(Location :: PROPERTY_PARENT, 0);
        $root_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $application);
        $root_conditions[] = new EqualityCondition(Location :: PROPERTY_TREE_TYPE, $tree_type);
        $root_conditions[] = new EqualityCondition(Location :: PROPERTY_TREE_IDENTIFIER, $tree_identifier);

        $root_condition = new AndCondition($root_conditions);

        $roots = $rdm->retrieve_locations($root_condition, null, 1);

        if ($roots->size() > 0)
        {
            return $roots->next_result();
        }
        else
        {
            return false;
        }
    }

    function get_root_id($application, $tree_type = 'root', $tree_identifier = 0)
    {
        $root = self :: get_root($application, $tree_type, $tree_identifier);
        return $root->get_id();
    }

    function get_location_by_identifier($application, $type, $identifier, $tree_identifier = '0', $tree_type = 'root')
    {
        $rdm = RightsDataManager :: get_instance();

        $conditions = array();
        $root_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $application);
        $root_conditions[] = new EqualityCondition(Location :: PROPERTY_TREE_TYPE, $tree_type);
        $root_conditions[] = new EqualityCondition(Location :: PROPERTY_TREE_IDENTIFIER, $tree_identifier);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_IDENTIFIER, $identifier);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_TYPE, $type);

        $condition = new AndCondition($conditions);

        $locations = $rdm->retrieve_locations($condition, 0, 1);

        return $locations->next_result();
    }

    function get_location_id_by_identifier($application, $type, $identifier, $tree_identifier = '0', $tree_type = 'root')
    {
        $location = self :: get_location_by_identifier($application, $type, $identifier, $tree_identifier, $tree_type);
        if (isset($location))
        {
            return $location->get_id();
        }
        else
        {
            // echo 'no location for that identifier ';
        }
        return null;
    }

    function get_rights_legend()
    {
        $html = array();

        $html[] = Utilities :: add_block_hider();
        $html[] = Utilities :: build_block_hider('rights_legend');
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_legend.png);">';
        $html[] = '<div class="title">' . Translation :: get('Legend') . '</div>';
        $html[] = '<ul class="rights_legend">';
        $html[] = '<li>' . Theme :: get_common_image('action_setting_true', 'png', Translation :: get('True')) . '</li>';
        $html[] = '<li>' . Theme :: get_common_image('action_setting_false', 'png', Translation :: get('False')) . '</li>';
        $html[] = '<li>' . Theme :: get_common_image('action_setting_true_locked', 'png', Translation :: get('LockedTrue')) . '</li>';
        $html[] = '<li>' . Theme :: get_common_image('action_setting_false_locked', 'png', Translation :: get('LockedFalse')) . '</li>';
        $html[] = '<li>' . Theme :: get_common_image('action_setting_true_inherit', 'png', Translation :: get('InheritedTrue')) . '</li>';
        $html[] = '<li>' . Theme :: get_common_image('action_setting_false_inherit', 'png', Translation :: get('InheritedFalse')) . '</li>';
        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = Utilities :: build_block_hider();

        return implode("\n", $html);
    }

    function invert_rights_template_right_location($right, $rights_template, $location)
    {
        if (isset($rights_template) && isset($right) && isset($location))
        {
            $rdm = RightsDataManager :: get_instance();
            $rights_template_right_location = $rdm->retrieve_rights_template_right_location($right, $rights_template, $location);

            if ($rights_template_right_location)
            {
                $rights_template_right_location->invert();
                return $rights_template_right_location->update();
            }
            else
            {
                $rights_template_right_location = new RightsTemplateRightLocation();
                $rights_template_right_location->set_location_id($location);
                $rights_template_right_location->set_right_id($right);
                $rights_template_right_location->set_rights_template_id($rights_template);
                $rights_template_right_location->set_value(1);
                return $rights_template_right_location->create();
            }
        }
        else
        {
            return false;
        }
    }

    function invert_user_right_location($right, $user, $location)
    {
        if (isset($user) && isset($right) && isset($location))
        {
            $rdm = RightsDataManager :: get_instance();
            $user_right_location = $rdm->retrieve_user_right_location($right, $user, $location);

            if ($user_right_location)
            {
                $user_right_location->invert();
                return $user_right_location->update();
            }
            else
            {
                $user_right_location = new UserRightLocation();
                $user_right_location->set_location_id($location);
                $user_right_location->set_right_id($right);
                $user_right_location->set_user_id($user);
                $user_right_location->set_value(1);
                return $user_right_location->create();
            }
        }
        else
        {
            return false;
        }
    }

    function invert_group_right_location($right, $group, $location)
    {
        if (isset($group) && isset($right) && isset($location))
        {
            $rdm = RightsDataManager :: get_instance();
            $group_right_location = $rdm->retrieve_group_right_location($right, $group, $location);

            if ($group_right_location)
            {
                $group_right_location->invert();
                return $group_right_location->update();
            }
            else
            {
                $group_right_location = new GroupRightLocation();
                $group_right_location->set_location_id($location);
                $group_right_location->set_right_id($right);
                $group_right_location->set_group_id($group);
                $group_right_location->set_value(1);
                return $group_right_location->create();
            }
        }
        else
        {
            return false;
        }
    }

	function set_rights_template_right_location_value($right, $rights_template, $location, $value)
    {
        if (isset($rights_template) && isset($right) && isset($location) && isset($value))
        {
            $rdm = RightsDataManager :: get_instance();
            $rights_template_right_location = $rdm->retrieve_rights_template_right_location($right, $rights_template, $location);

            if ($rights_template_right_location)
            {
                $rights_template_right_location->set_value($value);
                return $rights_template_right_location->update();
            }
            else
            {
                $rights_template_right_location = new RightsTemplateRightLocation();
                $rights_template_right_location->set_location_id($location);
                $rights_template_right_location->set_right_id($right);
                $rights_template_right_location->set_rights_template_id($rights_template);
                $rights_template_right_location->set_value($value);
                return $rights_template_right_location->create();
            }
        }
        else
        {
            return false;
        }
    }

    function set_user_right_location_value($right, $user, $location, $value)
    {
        if (isset($user) && isset($right) && isset($location) && isset($value))
        {
        	echo 'start<br />';
            $rdm = RightsDataManager :: get_instance();
            $user_right_location = $rdm->retrieve_user_right_location($right, $user, $location);

            if ($user_right_location)
            {
                $user_right_location->set_value($value);
                return $user_right_location->update();
            }
            else
            {
                $user_right_location = new UserRightLocation();
                $user_right_location->set_location_id($location);
                $user_right_location->set_right_id($right);
                $user_right_location->set_user_id($user);
                $user_right_location->set_value($value);
                return $user_right_location->create();
            }
        }
        else
        {
            return false;
        }
    }

    function set_group_right_location_value($right, $group, $location, $value)
    {
        if (isset($group) && isset($right) && isset($location) && isset($value))
        {
            $rdm = RightsDataManager :: get_instance();
            $group_right_location = $rdm->retrieve_group_right_location($right, $group, $location);

            if ($group_right_location)
            {
                $group_right_location->set_value($value);
                return $group_right_location->update();
            }
            else
            {
                $group_right_location = new GroupRightLocation();
                $group_right_location->set_location_id($location);
                $group_right_location->set_right_id($right);
                $group_right_location->set_group_id($group);
                $group_right_location->set_value($value);
                return $group_right_location->create();
            }
        }
        else
        {
            return false;
        }
    }

    function switch_location_lock($location)
    {
        $location->switch_lock();
        return $location->update();
    }

    function switch_location_inherit($location)
    {
        $location->switch_inherit();
        return $location->update();
    }

    static function rights_templates_for_element_finder($linked_rights_templates)
    {
        $rdm = RightsDataManager :: get_instance();
        $rights_templates = array();

        while ($linked_rights_template = $linked_rights_templates->next_result())
        {
            $rights_templates[] = $rdm->retrieve_rights_template($linked_rights_template->get_rights_template_id());
        }

        $return = array();

        foreach ($rights_templates as $rights_template)
        {
            $id = $rights_template->get_id();
            $return[$id] = self :: rights_template_for_element_finder($rights_template);
        }

        return $return;
    }

    static function rights_template_for_element_finder($rights_template)
    {
        $return = array();
        $return['id'] = $rights_template->get_id();
        $return['classes'] = 'type type_rights_template';
        $return['title'] = $rights_template->get_name();
        $return['description'] = strip_tags($rights_template->get_description());
        return $return;
    }

    function get_rights_template_right_location($right_id, $rights_template_id, $location_id)
    {
        $rdm = RightsDataManager :: get_instance();
        $object = $rdm->retrieve_rights_template_right_location($right_id, $rights_template_id, $location_id);

        if ($object)
        {
            return $object->get_value();
        }
        else
        {
            return 0;
        }
    }

    function get_user_right_location($right_id, $user_id, $location_id)
    {
        $rdm = RightsDataManager :: get_instance();
        $object = $rdm->retrieve_user_right_location($right_id, $user_id, $location_id);

        if ($object)
        {
            return $object->get_value();
        }
        else
        {
            return 0;
        }
    }

    function get_group_right_location($right_id, $group_id, $location_id)
    {
        $rdm = RightsDataManager :: get_instance();
        $object = $rdm->retrieve_group_right_location($right_id, $group_id, $location_id);

        if ($object)
        {
            return $object->get_value();
        }
        else
        {
            return 0;
        }
    }

    function get_rights_icon($location_url, $rights_url, $locked_parent, $right, $object, $location)
    {
        $type = Utilities :: camelcase_to_underscores(get_class($object));
        $get_function = 'get_' . $type . '_right_location';
        $allowed_function = 'is_allowed_for_' . $type;

        $html[] = '<div id="r_' . $right . '_' . $object->get_id() . '_' . $location->get_id() . '" style="float: left; width: 24%; text-align: center;">';
        if (isset($locked_parent))
        {
            $value = RightsUtilities :: $get_function($right, $object->get_id(), $locked_parent->get_id());
            $html[] = '<a href="' . $location_url . '">' . ($value == 1 ? '<img src="' . Theme :: get_common_image_path() . 'action_setting_true_locked.png" title="' . Translation :: get('LockedTrue') . '" />' : '<img src="' . Theme :: get_common_image_path() . 'action_setting_false_locked.png" title="' . Translation :: get('LockedFalse') . '" />') . '</a>';
        }
        else
        {
            $value = RightsUtilities :: $get_function($right, $object->get_id(), $location->get_id());

            if (! $value)
            {
                if ($location->inherits())
                {
                    $inherited_value = RightsUtilities :: $allowed_function($object->get_id(), $right, $location);

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

    function get_available_rights($application)
    {
        $base_path = (WebApplication :: is_application($application) ? (Path :: get_application_path() . 'lib/' . $application . '/') : (Path :: get(SYS_PATH) . $application . '/lib/'));
        $class = $application . '_rights.class.php';
        $file = $base_path . $class;

        if (! file_exists($file))
        {
            $rights = array();
        }
        else
        {
            require_once ($file);

            // TODO: When PHP 5.3 gets released, replace this by $class :: get_available_rights()
            $reflect = new ReflectionClass(Application :: application_to_class($application) . 'Rights');
            $rights = $reflect->getConstants();
            
			foreach($rights as $key => $right)
			{
				if(substr(strtolower($key), 0, 8) == 'location')
				{
					unset($rights[$key]);
				}
			}            
        }

        return $rights;
    }

    function get_allowed_users($right, $identifier, $type, $application = 'admin')
    {
        $rdm = RightsDataManager :: get_instance();

        $conditions = array();
        $conditions[] = new EqualityCondition('identifier', $identifier);
        $conditions[] = new EqualityCondition('type', $type);
        $conditions[] = new EqualityCondition('application', $application);
        $condition = new AndCondition($conditions);

        $location = $rdm->retrieve_locations($condition, 0, 1)->next_result();

        if(!is_null($location))
        {
        	$users = array();

	        $conditions = array();
	        $conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_RIGHT_ID, $right);
	        $conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_LOCATION_ID, $location->get_id());
	        $conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_VALUE, true);
	        $condition = new AndCondition($conditions);

	        $objects = $rdm->retrieve_user_right_locations($condition);

	        while($object = $objects->next_result())
	        {
	        	$users[] = $object->get_user_id();
	        }

	        return $users;
        }
        else
        {
        	return array();
        }
    }
}
?>