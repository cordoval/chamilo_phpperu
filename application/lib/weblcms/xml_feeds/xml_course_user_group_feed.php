<?php
/**
 * $Id: xml_course_user_group_feed.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.xml_feeds
 */
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course_group/course_group.class.php';

if (Authentication :: is_valid())
{
    $course = Request :: get('course');

    if ($course)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $course = $wdm->retrieve_course($course);

        $query = Request :: get('query');
        $exclude = Request :: get('exclude');

        $user_conditions = array();
        $group_conditions = array();

        if ($query)
        {
            $q = '*' . $query . '*';

            $user_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, $q);
            $group_conditions[] = new PatternMatchCondition(CourseGroup :: PROPERTY_NAME, $q);
        }

        if ($exclude)
        {
            if (! is_array($exclude))
            {
                $exclude = array($exclude);
            }

            $exclude_conditions = array();
            $exclude_conditions['user'] = array();
            $exclude_conditions['group'] = array();

            foreach ($exclude as $id)
            {
                $id = explode('_', $id);

                if ($id[0] == 'user')
                {
                    $condition = new NotCondition(new EqualityCondition(User :: PROPERTY_ID, $id[1]));
                }
                elseif ($id[0] == 'group')
                {
                    $condition = new NotCondition(new EqualityCondition(Group :: PROPERTY_ID, $id[1]));
                }

                $exclude_conditions[$id[0]][] = $condition;
            }

            if (count($exclude_conditions['user']) > 0)
            {
                $user_conditions[] = new AndCondition($exclude_conditions['user']);
            }

            if (count($exclude_conditions['group']) > 0)
            {
                $group_conditions[] = new AndCondition($exclude_conditions['group']);
            }
        }

        //if ($user_conditions)
        if (count($user_conditions) > 0)
        {
            $user_condition = new AndCondition($user_conditions);
        }
        else
        {
            $user_condition = null;
        }

        //if ($group_conditions)
        if (count($group_conditions) > 0)
        {
            $group_condition = new AndCondition($group_conditions);
        }
        else
        {
            $group_condition = null;
        }

        $udm = UserDataManager :: get_instance();
        $wdm = WeblcmsDataManager :: get_instance();

        $user_result_set = $udm->retrieve_users($user_condition);
        $relation_condition = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course);
        $course_user_relation_result_set = $wdm->retrieve_course_user_relations();

        $user_ids = array();
        while ($course_user = $course_user_relation_result_set->next_result())
        {
            $user_ids[] = $course_user->get_user();
        }

        $users = array();
        while ($user = $user_result_set->next_result())
        {
            if (in_array($user->get_id(), $user_ids))
            {
                $users[] = $user;
            }
        }

        $groups = array();
        $group_result_set = WeblcmsDataManager :: get_instance()->retrieve_course_groups($group_condition, null, null, array(new ObjectTableOrder(Group :: PROPERTY_NAME)));
        while ($group = $group_result_set->next_result())
        {

            $group_parent_id = $group->get_parent_id();

            if (!is_array($groups[$group_parent_id]))
            {
                $groups[$group_parent_id] = array();
            }

            if (!isset($groups[$group_parent_id][$group->get_id()]))
            {
                $groups[$group_parent_id][$group->get_id()] = $group;
            }

            if ($group_parent_id != 0)
            {
                $tree_parents = $group->get_parents(false);

                foreach ($tree_parents as $tree_parent)
                {
                    $tree_parent_parent_id = $tree_parent->get_parent_id();

                    if (!is_array($groups[$tree_parent_parent_id]))
                    {
                        $groups[$tree_parent_parent_id] = array();
                    }

                    if (!isset($groups[$tree_parent_parent_id][$tree_parent->get_id()]))
                    {
                        $groups[$tree_parent_parent_id][$tree_parent->get_id()] = $tree_parent;
                    }
                }
            }
        }

        $groups_tree = get_group_tree(1, $groups);
    }
    else
    {
        $users = array();
        $groups_tree = array();
    }
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="iso-8859-1"?>', "\n", '<tree>', "\n";

dump_tree($users, $groups_tree);

echo '</tree>';

function dump_tree($users, $groups_tree)
{
    if (contains_results($users) || contains_results($groups_tree))
    {
        if (contains_results($users))
        {
            echo '<node id="user" classes="category unlinked" title="Users">', "\n";
            foreach ($users as $user)
            {
                echo '<leaf id="user_' . $user->get_id() . '" classes="' . 'type type_user' . '" title="' . htmlentities($user->get_username()) . '" description="' . htmlentities($user->get_fullname()) . '"/>' . "\n";
            }
            echo '</node>', "\n";
        }

        if (contains_results($groups_tree))
        {
            global $course;

            echo '<node id="group" classes="category unlinked" title="'. $course->get_name() .'">', "\n";

            dump_groups_tree($groups_tree);
            echo '</node>', "\n";
        }
    }
}

function dump_groups_tree($groups)
{
    foreach($groups as $group)
    {
        if (contains_results($group['children']))
        {
            echo '<node id="group_' . $group['group']->get_id() . '" classes="type type_group" title="' . htmlspecialchars($group['group']->get_name()) . '" description="' . htmlspecialchars($group['group']->get_name()) . '">', "\n";
            dump_groups_tree($group['children']);
            echo '</node>', "\n";
        }
        else
        {
            echo '<leaf id="group_' . $group['group']->get_id() . '" classes="' . 'type type_group' . '" title="' . htmlspecialchars($group['group']->get_name()) . '" description="' . htmlspecialchars($group['group']->get_name()) . '"/>' . "\n";
        }
    }
}

function get_group_tree($index, $groups)
{
    $tree = array();
    foreach ($groups[$index] as $child)
    {
        $tree[] = array('group' => $child, 'children' => get_group_tree($child->get_id(), $groups));
    }
    return $tree;
}

function contains_results($objects)
{
    if (count($objects))
    {
        return true;
    }
    return false;
}
?>