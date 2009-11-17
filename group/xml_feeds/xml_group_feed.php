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
    
    //if ($group_conditions)
    if (count($group_conditions) > 0)
    {
        $group_condition = new AndCondition($group_conditions);
        
        $gdm = GroupDataManager :: get_instance();
        
        $filtered_groups = array();
        $group_result_set = $gdm->retrieve_groups($group_condition, null, null, array(new ObjectTableOrder(Group :: PROPERTY_NAME)));
        while ($group = $group_result_set->next_result())
        {
            //            dump($group);
            $filtered_groups[] = $group->get_id();
            $parents = $group->get_parents();
            
            while ($parent = $parents->next_result())
            {
                //                dump($parent);
                if (! in_array($parent->get_id(), $filtered_groups))
                {
                    $filtered_groups[] = $parent->get_id();
                }
            }
        }
        
        //        dump($filtered_groups);
        

        $groups = get_menu($filtered_groups);
    }
    else
    {
        $groups = get_menu();
    }
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n", '<tree>' . "\n";
echo dump_tree($groups);
echo '</tree>';

function dump_tree($groups)
{
    $html = array();
    
    if (contains_results($groups))
    {
        $html[] = '<node id="group" classes="type type_group" title="Groups">' . "\n";
        
        foreach ($groups as $group)
        {
            if (count($group['sub']) > 0)
            {
                $html[] = '<node id="group_' . $group['id'] . '" classes="type type_group" title="' . htmlspecialchars($group['title']) . '">' . "\n";
                $html[] = process_children($group);
                $html[] = '</node>' . "\n";
            }
            else
            {
                $html[] = '<leaf id="group_' . $group['id'] . '" classes="' . 'type type_group' . '" title="' . htmlspecialchars($group['title']) . '" description="' . htmlspecialchars($group['title']) . '"/>' . "\n";
            }
        }
        $html[] = '</node>' . "\n";
    }
    
    return implode("\n", $html);
}

function process_children($group)
{
    $html = array();
    $children = $group['sub'];
    
    foreach ($children as $child)
    {
        if (count($child['sub']) > 0)
        {
            $html[] = '<node id="group_' . $child['id'] . '" classes="type type_group" title="' . htmlspecialchars($child['title']) . '">' . "\n";
            $html[] = process_children($child);
            $html[] = '</node>' . "\n";
        }
        else
        {
            $html[] = '<leaf id="group_' . $child['id'] . '" classes="' . 'type type_group' . '" title="' . htmlspecialchars($child['title']) . '" description="' . htmlspecialchars($child['title']) . '"/>' . "\n";
        }
    }
    
    return implode("\n", $html);
}

function contains_results($objects)
{
    if (count($objects))
    {
        return true;
    }
    return false;
}

function get_menu($filtered_groups = array())
{
    $condition = new EqualityCondition(Group :: PROPERTY_PARENT, 0);
    $group = GroupDataManager :: get_instance()->retrieve_groups($condition, null, 1, new ObjectTableOrder(Group :: PROPERTY_NAME))->next_result();
    
    return get_menu_items($group->get_id(), $filtered_groups);
}

/**
 * Returns the menu items.
 * @param array $extra_items An array of extra tree items, added to the
 *                           root.
 * @return array An array with all menu items. The structure of this array
 *               is the structure needed by PEAR::HTML_Menu, on which this
 *               class is based.
 */
function get_menu_items($parent_id = 0, $filtered_groups = array())
{
    $conditions = array();
    $conditions[] = new EqualityCondition(Group :: PROPERTY_PARENT, $parent_id);
    
    if (count($filtered_groups) > 0)
    {
        $conditions[] = new InCondition(Group :: PROPERTY_ID, $filtered_groups);
    }
    
    $condition = new AndCondition($conditions);
    
    $groups = GroupDataManager :: get_instance()->retrieve_groups($condition, null, null, new ObjectTableOrder(Group :: PROPERTY_NAME));
    
    while ($group = $groups->next_result())
    {
        if (in_array($group->get_id(), $filtered_groups) || count($filtered_groups) == 0)
        {
            $menu_item = array();
            $menu_item[OptionsMenuRenderer :: KEY_ID] = $group->get_id();
            $menu_item['title'] = $group->get_name();
            
            if ($group->has_children())
            {
                $menu_item['sub'] = get_menu_items($group->get_id(), $filtered_groups);
            }
            else
            {
                $menu_item['sub'] = array();
            }
            
            $menu[$group->get_id()] = $menu_item;
        }
    }
    
    return $menu;
}
?>