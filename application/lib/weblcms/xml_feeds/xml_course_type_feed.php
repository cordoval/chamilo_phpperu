<?php
/**
 * $Id: application.lib.weblcms.xml_feeds.xml_course_type_feed.php 224 2009-11-13 14:40:30Z kariboe $
 * @package application.lib.weblcms.xml_feeds
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';

if (Authentication :: is_valid())
{
    $query = Request :: get('query');
    $exclude = Request :: get('exclude');

    $course_type_conditions = array();

    if ($query)
    {
        $q = '*' . $query . '*';
        $course_type_conditions[] = new PatternMatchCondition(CourseType :: PROPERTY_NAME, $q);
    }

    if ($exclude)
    {
        if (! is_array($exclude))
        {
            $exclude = array($exclude);
        }

        $exclude_conditions = array();
        $exclude_conditions['coursetype'] = array();

        foreach ($exclude as $id)
        {
            $id = explode('_', $id);

            if ($id[0] == 'coursetype')
            {
                $condition = new NotCondition(new EqualityCondition(CourseType :: PROPERTY_ID, $id[1]));
            }

            $exclude_conditions[$id[0]][] = $condition;
        }

        if (count($exclude_conditions['coursetype']) > 0)
        {
            $course_type_conditions[] = new AndCondition($exclude_conditions['coursetype']);
        }
    }
	$course_type_condition = new AndCondition($course_type_conditions, EqualityCondition(CourseType :: PROPERTY_ACTIVE, 1));
    $course_types = array();
    $course_types_result_set = WeblcmsDataManager :: get_instance()->retrieve_active_course_types($course_type_conditions, null, null, array(new ObjectTableOrder(Group :: PROPERTY_NAME)));
    while ($course_type = $course_types_result_set->next_result())
    {
        $group_parent_id = $group->get_parent();

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

            while ($tree_parent = $tree_parents->next_result())
            {
                $tree_parent_parent_id = $tree_parent->get_parent();

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

    $groups_tree = get_group_tree(0, $groups);
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n", '<tree>' . "\n";
echo dump_tree($groups_tree);
echo '</tree>';

function dump_tree($groups)
{
    $html = array();

    if (contains_results($groups))
    {
//        echo '<node id="group" classes="category unlinked" title="Groups">', "\n";
        dump_groups_tree($groups);
//        echo '</node>', "\n";
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