<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) . '/profiler_manager/profiler_manager.class.php';

/**
 * Provides the rights and locations of the profiler application
 *
 * @author Pieterjan Broekaert
 */
class ProfilerRights
{
    /*
     * The available rights
     */
    const RIGHT_PUBLISH = 1;
    const RIGHT_EDIT = 2;
    const RIGHT_DELETE = 3;
    const RIGHT_EDIT_RIGHTS = 4;

    /*
     * The tree that contains the dynamic locations
     */
    const TREE_TYPE_PROFILER = 1;

    /*
     * The type of locations available
     */
    const TYPE_CATEGORY = 1;
    const TYPE_PUBLICATION = 2;

    static function get_available_rights()
    {
        return array('Publish' => self :: RIGHT_PUBLISH, 'Edit' => self :: RIGHT_EDIT, 'Delete' => self::RIGHT_DELETE, 'Edit Rights' => self::RIGHT_EDIT_RIGHTS);
    }

//    static function is_allowed($right, $location, $type)
//    {
//        return RightsUtilities :: is_allowed($right, $location, $type, ProfilerManager :: APPLICATION_NAME);
//    }

    static function create_location_in_profiler_subtree($name, $identifier, $parent, $type)
    {
        return RightsUtilities :: create_location($name, ProfilerManager :: APPLICATION_NAME, $type, $identifier, 1, $parent, 0, 0, self :: TREE_TYPE_PROFILER);
    }

    static function get_profiler_subtree_root()
    {
        return RightsUtilities :: get_root(ProfilerManager :: APPLICATION_NAME, self :: TREE_TYPE_PROFILER, 0);
    }

    static function get_profiler_subtree_root_id()
    {
        return RightsUtilities :: get_root_id(ProfilerManager :: APPLICATION_NAME, self :: TREE_TYPE_PROFILER, 0);
    }

    static function get_location_id_by_identifier_from_profiler_subtree($identifier, $type)
    {
        return RightsUtilities :: get_location_id_by_identifier(ProfilerManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_PROFILER);
    }

    static function get_location_by_identifier_from_profiler_subtree($identifier, $type)
    {
        return RightsUtilities :: get_location_by_identifier(ProfilerManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_PROFILER);
    }

    static function is_allowed_in_profiler_subtree($right, $location, $type)
    {
        return self :: is_allowed($right, $location, $type, ProfilerManager :: APPLICATION_NAME, null, 0, self :: TREE_TYPE_PROFILER);
    }

    static function create_profiler_subtree_root_location()
    {
        return RightsUtilities :: create_location('profiler_tree', ProfilerManager :: APPLICATION_NAME, 0, 0, 0, 0, 0, 0, self :: TREE_TYPE_PROFILER);
    }


    static $user_cache;
    static $group_cache;
    static $template_cache;
    static $location_cache;
    static $location_parents_cache;
    static $right_granted_by_parent_cache;
    static $tree_identifier_cache;
    static $tree_type_cache;
    static $direct_parent_location_id_cache;

    static function is_allowed($right, $identifier = 0, $type = self :: TYPE_ROOT, $application = 'admin', $user_id = null, $tree_identifier = 0, $tree_type = self :: TREE_TYPE_ROOT)
    {
        // Determine the user_id of the user we're checking a right for
        $udm = UserDataManager :: get_instance();
        $user_id = $user_id ? $user_id : Session :: get_user_id();

        if (!self :: $user_cache[$user_id])
        {
            $user = $udm->retrieve_user($user_id);
            self :: $user_cache[$user_id] = $user;
        }
        else
        {
            $user = self :: $user_cache[$user_id];
        }

        if (!$user)
        {
            return false;
        }

        return self :: get_right($right, $identifier, $type, $application, $user, $tree_identifier, $tree_type);
    }

    static function get_right($right, $identifier, $type, $application, $user, $tree_identifier, $tree_type)
    {
        //if another location tree is checked, the location and right caching must be flushed
        if (is_null(self :: $tree_identifier_cache) || is_null(self :: $tree_type_cache))
        {
            self :: $tree_identifier_cache = $tree_identifier;
            self :: $tree_type_cache = $tree_type;
        }
        else
        {
            if (self :: $tree_identifier_cache != $tree_identifier || self :: $tree_type_cache != $tree_type)
            {
                self :: $location_parents_cache = array();
                self :: $right_granted_by_parent_cache = array();
                self :: $location_cache = array();
                self :: $tree_identifier_cache = $tree_identifier;
                self :: $tree_type_cache = $tree_type;
            }
        }


        if ($user instanceof User && $user->is_platform_admin())
        {
            return true;
        }

        if (!self :: $location_cache[$identifier])
        {
            $location = RightsUtilities :: get_location_by_identifier($application, $type, $identifier, $tree_identifier, $tree_type);
            $locked_parent = $location->get_locked_parent();

            if (isset($locked_parent))
            {
                $location = $locked_parent;
            }
            self :: $location_cache[$identifier] = $location;

            if (self :: $direct_parent_location_id_cache != $location->get_parent()) //not a sibling with previous checked location? => flush cache optimalisations for siblings
            {
                self :: $right_granted_by_parent_cache = array();
            }

            self :: $direct_parent_location_id_cache = $location->get_parent();
        }
        else
        {
            $location = self :: $location_cache[$identifier];
        }

        if (!$location)
        {
            return false;
        }

        if (self :: $right_granted_by_parent_cache[$right] == 1 && $location->inherits())
        {
            return true;
        }
        //has the user been given a direct right for this location?
        if (self :: is_allowed_for_user($user->get_id(), $right, $location))
        {
            return true;
        }

        if (!self :: $group_cache[$user->get_id()])
        {

            $query = 'select a.group_id,c.id as parent_id, d.rights_template_id
from `chamilo`.`group_group_rel_user` as a
join `chamilo`.`group_group` as b on a.group_id = b.id
join `chamilo`.`group_group` as c on c.left_value < b.left_value and c.right_value > b.right_value
left join `chamilo`.`group_group_rights_template` as d on d.group_id = a.group_id or c.id = d.group_id
where a.user_id = 4';

            $groups_and_templates = new ObjectResultSet($user->get_data_manager(), $user->get_data_manager()->query($query), $class_name = Utilities :: underscores_to_camelcase(GroupRightsTemplate :: get_table_name()));

            while ($record = $groups_and_templates->next_result())
            {
                if ($groups[$record->get_group_id()] != 1) //already processed?
                {
                    $groups[$record->get_group_id()] = 1;
                    if (self :: is_allowed_for_group($record->get_group_id(), $right, $location))
                    {
//                        self :: $right_cache[$right] = 1;
                        return true;
                    }
                }
                if ($groups[$record->get_optional_property('parent_id')] != 1) //already processed?
                {
                    $groups[$record->get_optional_property('parent_id')] = 1;
                    if (self :: is_allowed_for_group($record->get_optional_property('parent_id'), $right, $location))
                    {
//                        self :: $right_cache[$right] = 1;
                        return true;
                    }
                }
                if (!is_null($record->get_rights_template_id()) && $templates[$record->get_rights_template_id()] != 1)
                {
                    $templates[$record->get_rights_template_id()] = 1;
                    if (self :: is_allowed_for_rights_template($record->get_rights_template_id(), $right, $location))
                    {
//                        self :: $right_cache[$right] = 1;
                        return true;
                    }
                }
            }

            $user_templates = $user->get_rights_templates();
            while ($template = $user_templates->next_result())
            {
                if ($templates[$template->get_rights_template_id()] != 1)
                {
                    $templates[$template->get_rights_template_id()] = 1;
                    if (self :: is_allowed_for_rights_template($template->get_rights_template_id(), $right, $location))
                    {
//                        self :: $right_cache[$right] = 1;
                        return true;
                    }
                }
            }

            self :: $group_cache[$user->get_id()] = $groups;
            self :: $template_cache[$user->get_id()] = $templates;
        }
        else
        {
            $groups = self :: $group_cache[$user->get_id()];
            $templates = self :: $template_cache[$user->get_id()];

            foreach ($templates as $template => $value)
            {
                if (self :: $right_granted_by_parent_cache[$right] == -1) //the right wasnt granted in a previous run, this means that the parent locations will never grant the right
                {
                    if (RightsUtilities :: get_rights_template_right_location($right, $template, $location->get_id()))
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    if (self :: is_allowed_for_rights_template($template, $right, $location))
                    {
                        return true;
                    }
                }
            }
            foreach ($groups as $group => $value)
            {
                if (self :: $right_granted_by_parent_cache[$right] == -1) //the right wasnt granted in a previous run, this means that only the base location should be checked
                {
                    if (RightsUtilities :: get_rights_template_right_location($right, $group, $location->get_id()))
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    if (self :: is_allowed_for_group($group, $right, $location))
                    {
                        return true;
                    }
                }
            }
        }
        //if after the algorithm, the right_cache isnt set, this is saved for future requests on other sibling locations
        if (is_null(self :: $right_granted_by_parent_cache[$right]))
        {
            self :: $right_granted_by_parent_cache[$right] = -1;
        }
    }

    static function is_allowed_for_rights_template($rights_template, $right, $location)
    {
        $rdm = RightsDataManager :: get_instance();

        if (!self :: $location_parents_cache[$location])
        {
            $parents = $location->get_parents();
            self :: $location_parents_cache[$location] = $parents;
        }
        else
        {
            $parents = self :: $location_parents_cache[$location];
        }

        while ($parent = $parents->next_result())
        {
            $has_right = RightsUtilities :: get_rights_template_right_location($right, $rights_template, $parent->get_id());

            if ($has_right)
            {

                if ($parent->get_id() != $location->get_id()) //right is granted by parent locations
                {
                    self :: $right_granted_by_parent_cache[$right] = 1;
                }

                return true;
            }

            if (!$parent->inherits())
            {
                return false;
            }
        }

        return false;
    }

    static function is_allowed_for_user($user_id, $right, $location)
    {
        if (!self :: $location_parents_cache[$location])
        {
            $parents = $location->get_parents();
            self :: $location_parents_cache[$location] = $parents;
        }
        else
        {
            $parents = self :: $location_parents_cache[$location];
        }

        while ($parent = $parents->next_result())
        {

            $has_right = RightsUtilities :: get_user_right_location($right, $user_id, $parent->get_id());

            if ($has_right)
            {

                if ($parent->get_id() != $location->get_id()) //right is granted by parent locations
                {
                    self :: $right_granted_by_parent_cache[$right] = 1;
                }

                return true;
            }

            if (!$parent->inherits())
            {
                return false;
            }
        }

        return false;
    }

    static function is_allowed_for_group($group_id, $right, $location)
    {
        if (!self :: $location_parents_cache[$location])
        {
            $parents = $location->get_parents();
            self :: $location_parents_cache[$location] = $parents;
        }
        else
        {
            $parents = self :: $location_parents_cache[$location];
        }

        while ($parent = $parents->next_result())
        {
            $has_right = RightsUtilities :: get_group_right_location($right, $group_id, $parent->get_id());

            if ($has_right)
            {

                if ($parent->get_id() != $location->get_id()) //right is granted by parent locations
                {
                    self :: $right_granted_by_parent_cache[$right] = 1;
                }
                return true;
            }

            if (!$parent->inherits())
            {
                return false;
            }
        }

        return false;
    }

}

?>
