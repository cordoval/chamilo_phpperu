<?php
/**
 * $Id: xml_user_group_feed.php 170 2009-11-12 12:21:00Z vanpouckesven $
 * @package common.xml_feeds
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/../global.inc.php';

if (Authentication :: is_valid())
{
    $query = Request :: get('query');
    $exclude = Request :: get('exclude');

    $user_conditions = array();
    $group_conditions = array();

    if ($query)
    {
        $q = '*' . $query . '*';

        $user_conditions[] = new OrCondition(array(new PatternMatchCondition(User :: PROPERTY_USERNAME, $q), new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, $q), new PatternMatchCondition(User :: PROPERTY_LASTNAME, $q)));
        $group_conditions[] = new PatternMatchCondition(Group :: PROPERTY_NAME, $q);
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
    $gdm = GroupDataManager :: get_instance();

    $user_result_set = $udm->retrieve_users($user_condition, null, null, array(new ObjectTableOrder(User :: PROPERTY_LASTNAME), new ObjectTableOrder(User :: PROPERTY_FIRSTNAME)));

    $users = array();
    while ($user = $user_result_set->next_result())
    {
        $users[] = $user;
    }

    $groups = array();
    $group_result_set = $gdm->retrieve_groups($group_condition, null, null, array(new ObjectTableOrder(Group :: PROPERTY_NAME)));
    while ($group = $group_result_set->next_result())
    {
        $group_parent = $group->get_parent();
        if (is_array($groups[$group_parent]))
        {
            array_push($groups[$group_parent], $group);
        }
        else
        {
            $groups[$group_parent] = array($group);
        }
    }

    $groups_tree = get_group_tree(0, $groups);
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>', "\n", '<tree>', "\n";

dump_tree($users, $groups_tree);

echo '</tree>';

function dump_tree($users, $groups)
{
    if (contains_results($users) || contains_results($groups))
    {
        if (contains_results($users))
        {
            echo '<node id="user" classes="category unlinked" title="Users">', "\n";
            foreach ($users as $user)
            {
                echo '<leaf id="user_' . $user->get_id() . '" classes="' . 'type type_user' . '" title="' . htmlspecialchars($user->get_fullname()) . '" description="' . htmlentities($user->get_username()) . '"/>' . "\n";
            }
            echo '</node>', "\n";
        }

        if (contains_results($groups))
        {
            echo '<node id="group" classes="category unlinked" title="Groups">', "\n";

            dump_groups_tree($groups);
//            foreach ($groups as $group)
//            {
//                echo '<leaf id="group_' . $group->get_id() . '" classes="' . 'type type_group' . '" title="' . htmlspecialchars($group->get_name()) . '" description="' . htmlspecialchars($group->get_name()) . '"/>' . "\n";
//            }
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