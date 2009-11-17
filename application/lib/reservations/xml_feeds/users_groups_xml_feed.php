<?php
/**
 * $Id: users_groups_xml_feed.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.xml_feeds
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';

if (Authentication :: is_valid())
{
    $conditions = array();
    
    if (isset($_GET['query']))
    {
        $q = '*' . $_GET['query'] . '*';
        $query_condition = new PatternMatchCondition(User :: PROPERTY_USERNAME, $q);
        
        if (isset($query_condition))
        {
            $conditions[] = $query_condition;
        }
    }
    
    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $c[] = new EqualityCondition(User :: PROPERTY_USER_ID, $id);
        }
        $conditions[] = new NotCondition(new OrCondition($c));
    }
    
    if (isset($_GET['query']) || is_array($_GET['exclude']))
    {
        $condition = new AndCondition($conditions);
    }
    else
    {
        $condition = null;
    }
    
    $dm = UserDataManager :: get_instance();
    $objects = $dm->retrieve_users($condition);
    
    while ($lo = $objects->next_result())
    {
        $users[] = $lo;
    }
    
    $dm = GroupDataManager :: get_instance();
    $grs = $dm->retrieve_groups();
    while ($group = $grs->next_result())
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
        //echo '<node class="type_category unlinked" id="recipients" title="Recipients">';
        echo '<node id="user" class="type_category unlinked" title="Users">', "\n";
        foreach ($users as $lo)
        {
            echo '<leaf id="user_' . $lo->get_id() . '" class="' . 'type type_user' . '" title="' . htmlspecialchars($lo->get_username()) . '" description="' . htmlspecialchars($lo->get_firstname()) . ' ' . htmlentities($lo->get_lastname()) . '"/>' . "\n";
        }
        echo '</node>', "\n";
        
        echo '<node id="group" class="type_category unlinked" title="Groups">', "\n";
        foreach ($groups as $group)
        {
            echo '<leaf id="group_' . $group->get_id() . '" class="' . 'type type_group' . '" title="' . htmlspecialchars($group->get_name()) . '" description="' . htmlspecialchars($group->get_name()) . '"/>' . "\n";
        }
        echo '</node>', "\n";
        //echo '</node>';
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