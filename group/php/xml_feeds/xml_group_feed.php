<?php
/**
 * $Id: xml_group_feed.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.xml_feeds
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';

if (Authentication :: is_valid())
{
    $query = Request :: get('query');
    $exclude = Request :: get('exclude');

    $group_conditions = array();

    if ($query)
    {
        $q = '*' . $query . '*';
        $group_conditions[] = new PatternMatchCondition(Group :: PROPERTY_NAME, $q);
    }

    if ($exclude)
    {
        if (! is_array($exclude))
        {
            $exclude = array($exclude);
        }

        $exclude_conditions = array();
        $exclude_conditions['group'] = array();

        foreach ($exclude as $id)
        {
            $id = explode('_', $id);

            if ($id[0] == 'group')
            {
                $condition = new NotCondition(new EqualityCondition(Group :: PROPERTY_GROUP_ID, $id[1]));
            }

            $exclude_conditions[$id[0]][] = $condition;
        }

        if (count($exclude_conditions['group']) > 0)
        {
            $group_conditions[] = new AndCondition($exclude_conditions['group']);
        }
    }
	
    $group_condition = null;
    if(count($group_conditions)>1)
    	$group_condition = new AndCondition($group_conditions);
    elseif(count($group_conditions)==1)
    	$group_condition = $group_conditions[0];
    	
    $groups = array();
    $group_result_set = GroupDataManager :: get_instance()->retrieve_groups($group_condition, null, null, array(new ObjectTableOrder(Group :: PROPERTY_NAME)));
    while ($group = $group_result_set->next_result())
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