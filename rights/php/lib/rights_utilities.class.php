<?php
namespace rights;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\Session;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Theme;
use common\libraries\WebApplication;
use user\UserDataManager;
use user\User;
use rights\RightsUtilities;
use XML_Unserializer;
use PEAR;
use group\GroupDataManager;
use group\GroupRelUser;
use group\Group;
use group\GroupRightsTemplate;
use common\libraries\ObjectResultSet;

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
    const CONSTANT_RIGHT = 'RIGHT';
    const CONSTANT_TYPE = 'TYPE';

    const TREE_TYPE_ROOT = 0;
    const TYPE_ROOT = 0;

    protected static $is_allowed_cache;
    private static $constants;

    //    protected static $user_cache;


    static function create_location($name, $application, $type = self :: TYPE_ROOT, $identifier = 0, $inherit = 0, $parent = 0, $locked = 0, $tree_identifier = 0, $tree_type = self :: TREE_TYPE_ROOT, $return_location = false)
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

        if ($return_location && $succes)
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

        if ($xml === false)
        {
            return true;
        }
        else
        {
            $root = self :: create_location($xml['name'], $application, $xml['type'], $xml['identifier'], 0, 0, 0, 0, self :: TREE_TYPE_ROOT, true);
            if (! $root)
            {
                return false;
            }

            if (isset($xml['children']) && isset($xml['children']['location']) && count($xml['children']['location']) > 0)
            {
                self :: parse_tree($application, $xml, $root->get_id());
            }

            return true;
        }
    }

    static function create_subtree_root_location($application, $tree_identifier, $tree_type, $return_location = false)
    {
        return self :: create_location($tree_type, $application, self :: TYPE_ROOT, 0, 0, 0, 0, $tree_identifier, $tree_type, $return_location);
    }

    static function parse_locations_file($application)
    {
        $base_path = (WebApplication :: is_application($application) ? Path :: get_application_path() : Path :: get(SYS_PATH));
        $file = $base_path . $application . '/php/rights/' . $application . '_locations.xml';

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

            return $data;
        }
        else
        {
            return false;
        }
    }

    static function parse_tree($application, $xml, $parent)
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
            $location->set_tree_type(self :: TREE_TYPE_ROOT);
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

    /**
     * Added some caching to the is_allowed methods
     * Optimized for checking multiple locations
     * @author Pieterjan Broekaert
     */
    static $user_cache;
    static $group_cache;
    static $template_cache;
    static $location_cache;
    static $location_parents_cache; // here we cache the parents dataset=> todo optimalisation: only cache the parent_id's
    static $right_granted_by_parent_cache;
    static $tree_identifier_cache;
    static $tree_type_cache;
    static $direct_parent_location_id_cache;

    static function is_allowed($right, $identifier = 0, $type = self :: TYPE_ROOT, $application = 'admin', $user_id = null, $tree_identifier = 0, $tree_type = self :: TREE_TYPE_ROOT)
    {

        // Determine the user_id of the user we're checking a right for
        $user = self :: retrieve_user();
        $templates = self :: retrieve_templates($user);
        $groups = self :: retrieve_platform_groups($user);
        return self :: get_right($right, $identifier, $type, $application, $user, $templates, $groups, $tree_identifier, $tree_type);
    }

    /*
     * Helper function that retrieves the current user from db or cache
     */

    static function retrieve_user()
    {
        $udm = UserDataManager :: get_instance();
        $user_id = $user_id ? $user_id : Session :: get_user_id();

        if (! self :: $user_cache[$user_id])
        {
            $user = $udm->retrieve_user($user_id);
            self :: $user_cache[$user_id] = $user;
        }
        else
        {
            $user = self :: $user_cache[$user_id];
        }

        if (! $user)
        {
            throw new ErrorException("Rightserror: User not found");
        }
        return $user;
    }

    /*
     * Helper function that retrieves the templates (and also caches the platform groups)
     */

    static function retrieve_templates(User $user)
    {
        if (!self :: $group_cache[$user->get_id()]) //todo: if a user is not subscribed in any group, this check should also return true (avoid query with empty results)
        {

            $groups_and_templates = GroupDataManager::get_instance()->retrieve_all_groups_and_templates($user->get_id());
            while ($record = $groups_and_templates->next_result())
            {
                $groups[$record->get_optional_property('group_id')] = 1;
                $groups[$record->get_default_property('parent_id')] = 1;
                $templates[$record->get_optional_property('rights_template_id')] = 1;
            }

            $user_templates = $user->get_rights_templates();
            while ($template = $user_templates->next_result())
            {
                $templates[$template->get_rights_template_id()] = 1;
            }

            self :: $group_cache[$user->get_id()] = $groups;
            self :: $template_cache[$user->get_id()] = $templates;
        }
        return self :: $template_cache[$user->get_id()];
    }

    /*
     * Helper function that retrieves the platform groups
     * (the groups and templates are retrieved from db toghether for performance reasons)
     */

    static function retrieve_platform_groups(User $user)
    {
        if (!self :: $group_cache[$user->get_id()]) //todo: if a user is not subscribed in any group, this check should also return true (avoid query with empty results)
        {
            self :: retrieve_templates($user);
        }
        return self :: $group_cache[$user->get_id()];
    }

    static function get_right($right, $identifier, $type, $application, $user, $templates, $groups, $tree_identifier, $tree_type)
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

        if (! self :: $location_cache[$identifier][$type])
        {
            $location = self :: get_location_by_identifier($application, $type, $identifier, $tree_identifier, $tree_type);
            if (!$location)
            {
                throw new \ErrorException("RightsError: The requested location doesnt exist: " . $application . ';type=' . $type . ';location_id=' . $identifier . ';tree_id=' . $tree_identifier . ';tree_type=' . $tree_type);
            }
            $locked_parent = $location->get_locked_parent();

            if (isset($locked_parent))
            {
                $location = $locked_parent;
            }
            self :: $location_cache[$identifier][$type] = $location;

            $parent_location = $location->get_parent();

            if (self :: $direct_parent_location_id_cache != $parent_location) //not a sibling with previous checked location? => flush cache optimalisations for siblings
            {
                self :: $right_granted_by_parent_cache = array();
                self :: $location_parents_cache[$location->get_id()] = 0;
            }

            self :: $direct_parent_location_id_cache = $parent_location;
        }
        else
        {
            $location = self :: $location_cache[$identifier][$type];
        }

        if (self :: $right_granted_by_parent_cache[$right] == 1 && $location->inherits())
        {
            return true;
        }

        //has the user been given a direct right for this location?
        if (self :: $right_granted_by_parent_cache[$right] == - 1) //the right wasnt granted in a previous run, this means that only the base location should be checked
        {
            if (self :: get_user_right_location($right, $user->get_id(), $location->get_id()))
            {
                return true;
            }
        }
        else
        {
            if (self :: is_allowed_for_user($user->get_id(), $right, $location))
            {
                return true;
            }
        }

        foreach ($templates as $template => $value)
        {
            if (self :: $right_granted_by_parent_cache[$right] == -1) //the right wasnt granted in a previous run, this means that the parent locations will never grant the right
            {
                if (self :: get_rights_template_right_location($right, $template, $location->get_id()))
                {
                    return true;
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
                if (self :: get_group_right_location($right, $group, $location->get_id()))
                {
                    return true;
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

        //if after the algorithm, the right_cache isnt set, this is saved for future requests on other sibling locations
        if (is_null(self :: $right_granted_by_parent_cache[$right]))
        {
            self :: $right_granted_by_parent_cache[$right] = - 1;
        }
        return false;
    }

    static function is_allowed_for_rights_template($rights_template, $right, $location)
    {

        if (self :: $location_parents_cache[$location->get_id()] == 0)
        {
            $parents = $location->get_parents();
            self :: $location_parents_cache[$location->get_id()] = $parents->as_array();
        }
        else
        {
            $parents = self :: $location_parents_cache[$location->get_id()];
        }

        foreach ($parents as $parent)
        {
            $has_right = self :: get_rights_template_right_location($right, $rights_template, $parent->get_id());

            if ($has_right)
            {

                if ($parent->get_id() != $location->get_id()) //right is granted by parent locations
                {
                    self :: $right_granted_by_parent_cache[$right] = 1;
                }

                return true;
            }

            if (! $parent->inherits())
            {
                return false;
            }
        }

        return false;
    }

    static function is_allowed_for_user($user_id, $right, $location)
    {
        if (self :: $location_parents_cache[$location->get_id()] == 0)
        {
            $parents = $location->get_parents()->as_array();
            self :: $location_parents_cache[$location->get_id()] = $parents;
        }
        else
        {
            $parents = self :: $location_parents_cache[$location->get_id()];
        }

        foreach ($parents as $parent)
        {

            $has_right = self :: get_user_right_location($right, $user_id, $parent->get_id());

            if ($has_right)
            {

                if ($parent->get_id() != $location->get_id()) //right is granted by parent locations
                {
                    self :: $right_granted_by_parent_cache[$right] = 1;
                }

                return true;
            }

            if (! $parent->inherits())
            {
                return false;
            }
        }
    }

    static function is_allowed_for_group($group_id, $right, $location)
    {
        if (self :: $location_parents_cache[$location->get_id()] == 0)
        {
            $parents = $location->get_parents()->as_array();
            self :: $location_parents_cache[$location->get_id()] = $parents;
        }
        else
        {
            $parents = self :: $location_parents_cache[$location->get_id()];
        }

        foreach ($parents as $parent)
        {
            $has_right = static :: get_group_right_location($right, $group_id, $parent->get_id());

            if ($has_right)
            {

                if ($parent->get_id() != $location->get_id()) //right is granted by parent locations
                {
                    self :: $right_granted_by_parent_cache[$right] = 1;
                }
                return true;
            }

            if (! $parent->inherits())
            {
                return false;
            }
        }

        return false;
    }

    //    //eduard changed second param $location  for $identifier
    //    static function is_allowed($right, $identifier = 0, $type = self :: TYPE_ROOT, $application = 'admin', $user_id = null, $tree_identifier = 0, $tree_type = self :: TREE_TYPE_ROOT)
    //    {
    //        // Determine the user_id of the user we're checking a right for
    //        $udm = UserDataManager :: get_instance();
    //        $user_id = $user_id ? $user_id : Session :: get_user_id();
    //
    //        if (! self :: $user_cache[$user_id])
    //        {
    //            $user = $udm->retrieve_user($user_id);
    //            self :: $user_cache[$user_id] = $user;
    //        }
    //        else
    //        {
    //            $user = self :: $user_cache[$user_id];
    //        }
    //
    //        if (! $user)
    //        {
    //            return false;
    //        }
    //
    //        $cache_id = md5(serialize(array($right, $identifier, $type, $application, $user_id, $tree_identifier, $tree_type)));
    //
    //        if (! isset(self :: $is_allowed_cache[$cache_id]))
    //        {
    //            self :: $is_allowed_cache[$cache_id] = self :: get_right($right, $identifier, $type, $application, $user, $tree_identifier, $tree_type);
    //        }
    //
    //        return self :: $is_allowed_cache[$cache_id];
    //    }
    //
    //    /**
    //     * @param int $right
    //     * @param int $location
    //     eduard: changed second param $location for $identifier
    //     * @param string $type
    //     * @param string $application
    //     * @param User $user
    //     * @param int $tree_identifier
    //     * @param string $tree_type
    //     * @return boolean
    //     */
    //    static function get_right($right, $identifier, $type, $application, $user, $tree_identifier, $tree_type)
    //    {
    //
    //        if ($user instanceof User && $user->is_platform_admin())
    //        {
    //            return true;
    //        }
    //
    //        $location = self :: get_location_by_identifier($application, $type, $identifier, $tree_identifier, $tree_type);
    //        if (! $location)
    //        {
    //            return false;
    //        }
    //
    //        $locked_parent = $location->get_locked_parent();
    //        if (isset($locked_parent))
    //        {
    //            $location = $locked_parent;
    //        }
    //
    //        if (isset($user))
    //        {
    //            // Check right for the user's groups
    //            $user_groups = $user->get_groups();
    //
    //            if (! is_null($user_groups))
    //            {
    //                while ($group = $user_groups->next_result())
    //                {
    //                    $group_templates = $group->get_rights_templates();
    //
    //                    while ($group_template = $group_templates->next_result())
    //                    {
    //                        if (self :: is_allowed_for_rights_template($group_template->get_rights_template_id(), $right, $location))
    //                                                {
    //                            return true;
    //                        }
    //                    }
    //
    //                    if (self :: is_allowed_for_group($group->get_id(), $right, $location))
    //                    {
    //                        return true;
    //                    }
    //                }
    //            }
    //
    //            // Check right for the individual user's configured templates
    //            $user_templates = $user->get_rights_templates();
    //
    //            while ($user_template = $user_templates->next_result())
    //            {
    //                if (self :: is_allowed_for_rights_template($user_template->get_rights_template_id(), $right, $location))
    //                {
    //                    return true;
    //                }
    //            }
    //
    //            if (self :: is_allowed_for_user($user->get_id(), $right, $location))
    //            {
    //                return true;
    //            }
    //        }
    //        else
    //        {
    //            // TODO: Use anonymous user for this, he may or may not have some rights too
    //            return false;
    //        }
    //
    //        return false;
    //    }
    //
    //    static function is_allowed_for_rights_template($rights_template, $right, $location)
    //    {
    //        $rdm = RightsDataManager :: get_instance();
    //
    //        $parents = $location->get_parents();
    //
    //        while ($parent = $parents->next_result())
    //        {
    //            $has_right = self :: get_rights_template_right_location($right, $rights_template, $parent->get_id());
    //
    //            if ($has_right)
    //            {
    //                return true;
    //            }
    //
    //            if (! $parent->inherits())
    //            {
    //                return false;
    //            }
    //        }
    //
    //        return false;
    //    }
    //
    //    static function is_allowed_for_user($user, $right, $location)
    //    {
    //        $parents = $location->get_parents();
    //
    //        while ($parent = $parents->next_result())
    //        {
    //            $has_right = self :: get_user_right_location($right, $user, $parent->get_id());
    //
    //            if ($has_right)
    //            {
    //                return true;
    //            }
    //
    //            if (! $parent->inherits())
    //            {
    //                return false;
    //            }
    //        }
    //
    //        return false;
    //    }
    //
    //    static function is_allowed_for_group($group, $right, $location)
    //    {
    //        $parents = $location->get_parents();
    //
    //        while ($parent = $parents->next_result())
    //        {
    //            $has_right = self :: get_group_right_location($right, $group, $parent->get_id());
    //
    //            if ($has_right)
    //            {
    //                return true;
    //            }
    //
    //            if (! $parent->inherits())
    //            {
    //                return false;
    //            }
    //        }
    //
    //        return false;
    //    }
    //
    static function move_multiple($locations, $new_parent_id, $new_previous_id = 0)
    {
        $rdm = RightsDatatamanager :: get_instance();

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

    static function get_root($application, $tree_type = self :: TREE_TYPE_ROOT, $tree_identifier = 0)
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

    static function get_root_id($application, $tree_type = self :: TREE_TYPE_ROOT, $tree_identifier = 0)
    {
        $root = self :: get_root($application, $tree_type, $tree_identifier);
        if ($root)
        {
            return $root->get_id();
        }
        else
        {
            return false;
        }
    }

    static function get_location_by_identifier($application, $type, $identifier, $tree_identifier = '0', $tree_type = self :: TREE_TYPE_ROOT)
    {
        $rdm = RightsDataManager :: get_instance();

        //nathalie: changed this method slightly because the first 3 conditions were not taken into account
        $conditions = array();
        $conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $application);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_TREE_TYPE, $tree_type);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_TREE_IDENTIFIER, $tree_identifier);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_IDENTIFIER, $identifier);
        //nathalie: added this check so the type becomes optional (I need this method to work with variable types)
        if ($type != null)
        {
            $conditions[] = new EqualityCondition(Location :: PROPERTY_TYPE, $type);
        }
        $condition = new AndCondition($conditions);
        $locations = $rdm->retrieve_locations($condition, 0, 1);

        return $locations->next_result();
    }

    static function get_location_id_by_identifier($application, $type, $identifier, $tree_identifier = '0', $tree_type = self :: TREE_TYPE_ROOT)
    {
        $location = self :: get_location_by_identifier($application, $type, $identifier, $tree_identifier, $tree_type);
        if (isset($location))
        {
            return $location->get_id();
        }
        else
        {
            return 0;
        }
    }

    static function get_rights_legend()
    {
        $html = array();

        $html[] = Utilities :: add_block_hider();
        $html[] = Utilities :: build_block_hider('rights_legend');
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_legend.png);">';
        $html[] = '<div class="title">' . Translation :: get('Legend') . '</div>';
        $html[] = '<ul class="rights_legend">';
        $html[] = '<li>' . Theme :: get_common_image('action_setting_true', 'png', Translation :: get('ConfirmTrue', null, Utilities :: COMMON_LIBRARIES)) . '</li>';
        $html[] = '<li>' . Theme :: get_common_image('action_setting_false', 'png', Translation :: get('ConfirmFalse', null, Utilities :: COMMON_LIBRARIES)) . '</li>';
        $html[] = '<li>' . Theme :: get_common_image('action_setting_true_locked', 'png', Translation :: get('LockedTrue')) . '</li>';
        $html[] = '<li>' . Theme :: get_common_image('action_setting_false_locked', 'png', Translation :: get('LockedFalse')) . '</li>';
        $html[] = '<li>' . Theme :: get_common_image('action_setting_true_inherit', 'png', Translation :: get('InheritedTrue')) . '</li>';
        $html[] = '<li>' . Theme :: get_common_image('action_setting_false_inherit', 'png', Translation :: get('InheritedFalse')) . '</li>';
        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = Utilities :: build_block_hider();

        return implode("\n", $html);
    }

    static function invert_rights_template_right_location($right, $rights_template, $location)
    {
        if (! empty($rights_template) && ! empty($right) && ! empty($location))
        {
            $rdm = RightsDataManager :: get_instance();
            $rights_template_right_location = $rdm->retrieve_rights_template_right_location($right, $rights_template, $location);

            if ($rights_template_right_location)
            {
                if ($rights_template_right_location->is_enabled())
                {
                    return $rights_template_right_location->delete();
                }
                else
                {
                    $rights_template_right_location->invert();
                    return $rights_template_right_location->update();
                }
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

    static function invert_user_right_location($right, $user, $location)
    {
        if (! empty($user) && ! empty($right) && ! empty($location))
        {
            $rdm = RightsDataManager :: get_instance();
            $user_right_location = $rdm->retrieve_user_right_location($right, $user, $location);

            if ($user_right_location)
            {
                if ($user_right_location->is_enabled())
                {
                    return $user_right_location->delete();
                }
                else
                {
                    $user_right_location->invert();
                    return $user_right_location->update();
                }
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

    static function invert_group_right_location($right, $group, $location)
    {
        if (! empty($group) && ! empty($right) && ! empty($location))
        {
            $rdm = RightsDataManager :: get_instance();
            $group_right_location = $rdm->retrieve_group_right_location($right, $group, $location);

            if ($group_right_location)
            {
                if ($group_right_location->is_enabled())
                {
                    return $group_right_location->delete();
                }
                else
                {
                    $group_right_location->invert();
                    return $group_right_location->update();
                }
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

    static function set_rights_template_right_location_value($right, $rights_template, $location, $value)
    {
        if (! empty($rights_template) && ! empty($right) && ! empty($location) && isset($value))
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

    static function set_user_right_location_value($right, $user, $location, $value)
    {
        if (! empty($user) && ! empty($right) && ! empty($location) && isset($value))
        {
            $rdm = RightsDataManager :: get_instance();
            $user_right_location = $rdm->retrieve_user_right_location($right, $user, $location);

            if ($user_right_location)
            {
                if ($value == true)
                {
                    $user_right_location->set_value($value);
                    return $user_right_location->update();
                }
                else
                {
                    return $user_right_location->delete();
                }
            }
            else
            {
                if ($value == true)
                {
                    $user_right_location = new UserRightLocation();
                    $user_right_location->set_location_id($location);
                    $user_right_location->set_right_id($right);
                    $user_right_location->set_user_id($user);
                    $user_right_location->set_value($value);
                    return $user_right_location->create();
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

    static function set_group_right_location_value($right, $group, $location, $value)
    {
        if (! empty($group) && ! empty($right) && ! empty($location) && isset($value))
        {
            $rdm = RightsDataManager :: get_instance();
            $group_right_location = $rdm->retrieve_group_right_location($right, $group, $location);

            if ($group_right_location)
            {
                if ($value == true)
                {
                    $group_right_location->set_value($value);
                    return $group_right_location->update();
                }
                else
                {
                    return $group_right_location->delete();
                }
            }
            else
            {
                if ($value == true)
                {
                    $group_right_location = new GroupRightLocation();
                    $group_right_location->set_location_id($location);
                    $group_right_location->set_right_id($right);
                    $group_right_location->set_group_id($group);
                    $group_right_location->set_value($value);
                    return $group_right_location->create();
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

    static function switch_location_lock($location)
    {
        $location->switch_lock();
        return $location->update();
    }

    static function switch_location_inherit($location)
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

    static function get_rights_template_right_location($right_id, $rights_template_id, $location_id)
    {
        $rdm = RightsDataManager :: get_instance();
        $object = $rdm->retrieve_rights_template_right_location($right_id, $rights_template_id, $location_id);

        if ($object instanceof RightsTemplateRightLocation)
        {
            return $object->get_value();
        }
        else
        {
            return 0;
        }
    }

    static function get_user_right_location($right_id, $user_id, $location_id)
    {
        $rdm = RightsDataManager :: get_instance();
        $object = $rdm->retrieve_user_right_location($right_id, $user_id, $location_id);

        if ($object instanceof UserRightLocation)
        {
            return $object->get_value();
        }
        else
        {
            return 0;
        }
    }

    static function get_group_right_location($right_id, $group_id, $location_id)
    {
        $rdm = RightsDataManager :: get_instance();
        $object = $rdm->retrieve_group_right_location($right_id, $group_id, $location_id);

        if ($object instanceof GroupRightLocation)
        {
            return $object->get_value();
        }
        else
        {
            return 0;
        }
    }

    static function get_rights_icon($location_url, $rights_url, $locked_parent, $right, $object, $location)
    {
        $type = Utilities :: get_classname_from_object($object, true);
        $get_function = 'get_' . $type . '_right_location';
        $allowed_function = 'is_allowed_for_' . $type;

        $html[] = '<div id="r_' . $right . '_' . $object->get_id() . '_' . $location->get_id() . '" style="float: left; width: 24%; text-align: center;">';
        if (isset($locked_parent))
        {
            $value = self :: $get_function($right, $object->get_id(), $locked_parent->get_id());
            $html[] = '<a href="' . $location_url . '">' . ($value == 1 ? '<img src="' . Theme :: get_common_image_path() . 'action_setting_true_locked.png" title="' . Translation :: get('LockedTrue') . '" />' : '<img src="' . Theme :: get_common_image_path() . 'action_setting_false_locked.png" title="' . Translation :: get('LockedFalse') . '" />') . '</a>';
        }
        else
        {
            $value = self :: $get_function($right, $object->get_id(), $location->get_id());

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

    static function get_available_constants($application, $type = self :: CONSTANT_RIGHT)
    {
        if (! isset(self :: $constants))
        {
            $base_path = (WebApplication :: is_application($application) ? (Path :: get_application_path() . 'lib/' . $application . '/') : (Path :: get(SYS_PATH) . $application . '/lib/'));
            $class = $application . '_rights.class.php';
            $class_name = Application :: application_to_class($application) . 'Rights';

            $file = $base_path . $class;

            if (! file_exists($file))
            {
                self :: $constants[$application] = array();
            }
            else
            {
                require_once ($file);

                $reflect = new ReflectionClass($class_name);
                $constants = $reflect->getConstants();

                foreach ($constants as $name => $value)
                {
                    $parts = explode('_', $name);

                    if (! is_array(self :: $constants[$application][$parts[0]]))
                    {
                        self :: $constants[$application][$parts[0]] = array();
                    }

                    self :: $constants[$application][$parts[0]][$name] = $value;
                }
            }
        }
        return self :: $constants[$application][$type];
    }

    static function get_available_rights($application)
    {
        return self :: get_available_constants($application, self :: CONSTANT_RIGHT);
    }

    static function get_available_types($application)
    {
        return self :: get_available_constants($application, self :: CONSTANT_TYPE);
    }

    static function get_allowed_users($right, $identifier, $type, $application = 'admin')
    {
        $rdm = RightsDataManager :: get_instance();

        $conditions = array();
        $conditions[] = new EqualityCondition('identifier', $identifier);
        $conditions[] = new EqualityCondition('type', $type);
        $conditions[] = new EqualityCondition('application', $application);
        $condition = new AndCondition($conditions);

        $location = $rdm->retrieve_locations($condition, 0, 1)->next_result();

        if (! is_null($location))
        {
            $users = array();

            $conditions = array();
            $conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_RIGHT_ID, $right);
            $conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_LOCATION_ID, $location->get_id());
            $conditions[] = new EqualityCondition(UserRightLocation :: PROPERTY_VALUE, true);
            $condition = new AndCondition($conditions);

            $objects = $rdm->retrieve_user_right_locations($condition);

            while ($object = $objects->next_result())
            {
                $users[] = $object->get_user_id();
            }

            $gdm = GroupDataManager :: get_instance();

            $conditions = array();
            $conditions[] = new EqualityCondition(GroupRightLocation :: PROPERTY_RIGHT_ID, $right);
            $conditions[] = new EqualityCondition(GroupRightLocation :: PROPERTY_LOCATION_ID, $location->get_id());
            $conditions[] = new EqualityCondition(GroupRightLocation :: PROPERTY_VALUE, true);
            $condition = new AndCondition($conditions);

            $objects = $rdm->retrieve_group_right_locations($condition);
            $group_users = array();
            while ($object = $objects->next_result())
            {
                $group = $gdm->retrieve_group($object->get_group_id());
                $group_users = array_merge($group_users, $group->get_users(true));
            }
            $users = array_merge($users, $group_users);
            return array_unique($users);
        }
        else
        {
            return array();
        }
    }

    static function get_allowed_locations($rights = array(), $application, $type, $user_id, $tree_identifier = null, $tree_type = null)
    {
    /**
     * First idea
     *
     * Retrieve the locations from the application and the given type, optionally treeidentifier and tree type, and add them to an array
     * For each of the locations, retrieve the parents and add them to the array (only if parent is unique)
     * Retrieve all the user groups
     * Retrieve al the user templates
     *
     * Retrieve all the locations joined with the user location relation table and group location relation table and template location relation table where
     * the user, the rights, the groups, the templates and the given locations are in from the given type in the given tree
     *
     * return the identifiers of these locations
     *
     * Second idea
     *
     * Retrieve all user groups
     * Retrieve all the user templates
     * Retrieve all the locations where a user has a right in the user location relation, group location relation, template location relation  in a given tree_type / tree_identifier
     *
     * Return the identifiers where the locations type are equal to the given type
     */
    }

}

?>
