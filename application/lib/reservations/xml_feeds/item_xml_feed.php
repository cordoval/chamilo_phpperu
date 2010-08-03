<?php
/**
 * $Id: item_xml_feed.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.xml_feeds
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/reservations/item.class.php';
require_once Path :: get_application_path() . 'lib/reservations/reservations_data_manager.class.php';

if (Authentication :: is_valid())
{
    $conditions = array();
    
    $conditions[] = new EqualityCondition(Item :: PROPERTY_STATUS, Item :: STATUS_NORMAL);
    
    if (isset($_GET['query']))
    {
        $q = '*' . $_GET['query'] . '*';
        $query_condition = new PatternMatchCondition(Item :: PROPERTY_NAME, $q);
        
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
            $c[] = new EqualityCondition(Item :: PROPERTY_ID, $id);
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
    
    $dm = ReservationsDataManager :: get_instance();
    $objects = $dm->retrieve_items($condition);
    
    while ($item = $objects->next_result())
    {
        $items[] = $item;
    }
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="iso-8859-1"?>', "\n", '<tree>', "\n";

dump_tree($items);

echo '</tree>';

function dump_tree($items)
{
    if (contains_results($items))
    {
        echo '<node class="type_category unlinked" id="items" title="Items">';
        foreach ($items as $item)
        {
            echo '<leaf id="' . $item->get_id() . '" class="' . 'type type_group' . '" title="' . htmlspecialchars($item->get_name()) . '" description="' . htmlspecialchars($item->get_name()) . '"/>' . "\n";
        }
        echo '</node>';
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