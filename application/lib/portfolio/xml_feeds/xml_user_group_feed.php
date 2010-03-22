<?php
/**
 * @package application.lib.personal_messenger
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course_group/course_group.class.php';

if (Authentication :: is_valid())
{
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

    $user_result_set = $udm->retrieve_users($user_condition);

    $users = array();
    while ($user = $user_result_set->next_result())
    {
        $users[] = $user;
    }

    $groups = array();
    $group_result_set = $gdm->retrieve_groups($group_condition);
    while ($group = $group_result_set->next_result())
    {
        $groups[] = $group;
    }

}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="iso-8859-1"?>', "\n", '<tree>', "\n";

dump_tree($users, $groups);

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
                echo '<leaf id="user_' . $user->get_id() . '" classes="' . 'type type_user' . '" title="' . htmlentities($user->get_username()) . '" description="' . htmlentities($user->get_fullname()) . '"/>' . "\n";
            }
            echo '</node>', "\n";
        }

        if (contains_results($groups))
        {
            echo '<node id="group" classes="category unlinked" title="Groups">', "\n";
            foreach ($groups as $group)
            {
                echo '<leaf id="group_' . $group->get_id() . '" classes="' . 'type type_group' . '" title="' . htmlentities($group->get_name()) . '" description="' . htmlentities($group->get_name()) . '"/>' . "\n";
            }
            echo '</node>', "\n";
        }
    }
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