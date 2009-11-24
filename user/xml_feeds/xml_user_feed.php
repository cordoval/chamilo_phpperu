<?php
/**
 * $Id: xml_user_feed.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.xml_feeds
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';

Translation :: set_application('user');

if (Authentication :: is_valid())
{
    $conditions = array();

    $query_condition = Utilities :: query_to_condition($_GET['query'], array(User :: PROPERTY_USERNAME, User :: PROPERTY_FIRSTNAME, User :: PROPERTY_LASTNAME));
    if (isset($query_condition))
    {
        $conditions[] = $query_condition;
    }

    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $c[] = new EqualityCondition(User :: PROPERTY_ID, $id);
        }
        $conditions[] = new NotCondition(new OrCondition($c));
    }

    if (count($conditions) > 0)
    {
        $condition = new AndCondition($conditions);
    }
    else
    {
        $condition = null;
    }

    $udm = UserDataManager :: get_instance();
    $users = $udm->retrieve_users($condition, null, null, array(new ObjectTableOrder(User :: PROPERTY_LASTNAME), new ObjectTableOrder(User :: PROPERTY_FIRSTNAME)));
}
else
{
    $users = null;
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<tree>', "\n";

if (isset($users))
{
    dump_tree($users);
}

echo '</tree>';

function dump_tree($users)
{
    if (isset($users) && $users->size() == 0)
    {
        return;
    }

    echo '<node id="0" classes="type_category unlinked" title="' . Translation :: get('Users') . '">' . "\n";

    while ($user = $users->next_result())
    {
        echo '<leaf id="' . $user->get_id() . '" classes="type type_user" title="' . htmlspecialchars($user->get_fullname()) . '" description="' . htmlspecialchars($user->get_username()) . '"/>' . "\n";
    }

    echo '</node>' . "\n";
}

function contains_results($node, $objects)
{
    if (count($objects[$node['obj']->get_id()]))
    {
        return true;
    }
    foreach ($node['sub'] as $child)
    {
        if (contains_results($child, $objects))
        {
            return true;
        }
    }
    return false;
}
?>