<?php
/**
 * @package repository
 */
require_once dirname(__FILE__) . '/../common/global.inc.php';
require_once dirname(__FILE__) . '/lib/category_manager/repository_category.class.php';

Translation :: set_application('repository');

if (Authentication :: is_valid())
{
    $conditions = array();

    $query_condition = Utilities :: query_to_condition(Request :: get('query'), ContentObject :: PROPERTY_TITLE);
    if (isset($query_condition))
    {
        $conditions[] = $query_condition;
    }

    $owner_condition = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id());
    $conditions[] = $owner_condition;

    if (is_array(Request :: get('exclude')))
    {
        $c = array();
        foreach (Request :: get('exclude') as $id)
        {
            $c[] = new EqualityCondition(ContentObject :: PROPERTY_ID, $id, ContentObject :: get_table_name());
        }
        $conditions[] = new NotCondition(new OrCondition($c));
    }

    $condition = new AndCondition($conditions);

    $dm = RepositoryDataManager :: get_instance();
    $order_property[] = new ObjectTableOrder(ContentObject :: PROPERTY_TITLE);
    $objects = $dm->retrieve_content_objects($condition, $order_property);

    while ($lo = $objects->next_result())
    {
        /*$cat = $dm->retrieve_categories(new EqualityCondition('id', $lo->get_parent_id()))->next_result();
		$cid = $cat->get_id();*/
        $cid = $lo->get_parent_id();
        if (is_array($objects_by_cat[$cid]))
        {
            array_push($objects_by_cat[$cid], $lo);
        }
        else
        {
            $objects_by_cat[$cid] = array($lo);
        }
    }

    $categories = array();
    $root = new RepositoryCategory();
    $root->set_id(0);
    $root->set_name(Translation :: get('MyRepository'));
    $root->set_parent(- 1);
    $categories[- 1] = array($root);

    $cats = $dm->retrieve_categories(new EqualityCondition('user_id', Session :: get_user_id()));
    while ($cat = $cats->next_result())
    {
        $parent = $cat->get_parent();
        if (is_array($categories[$parent]))
        {
            array_push($categories[$parent], $cat);
        }
        else
        {
            $categories[$parent] = array($cat);
        }
    }

    $tree = get_tree(- 1, $categories);
}
else
{
    $tree = null;
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

if (isset($tree))
{
    dump_tree($tree, $objects_by_cat);
}

echo '</tree>';

function get_tree($index, $flat_tree)
{
    $tree = array();
    foreach ($flat_tree[$index] as $child)
    {
        $tree[] = array('obj' => $child, 'sub' => get_tree($child->get_id(), $flat_tree));
    }
    return $tree;
}

function dump_tree($tree, $objects)
{
    if (! count($tree))
    {
        return;
    }
    foreach ($tree as $node)
    {
        if (! contains_results($node, $objects))
        {
            continue;
        }
        $id = $node['obj']->get_id();
        if (get_class($node['obj']) == 'RepositoryCategory')
        {
            $title = $node['obj']->get_name();
        }
        else
        {
            $title = $node['obj']->get_title();
        }

        echo '<node id="category_' . $id . '" classes="category unlinked" title="' . htmlspecialchars($title) . '">' . "\n";
        dump_tree($node['sub'], $objects);

        foreach ($objects[$id] as $lo)
        {
            $id = $lo->get_id();
            $value = Utilities :: content_object_for_element_finder($lo);
            echo '<leaf id="lo_' . $id . '" classes="' . $value['classes'] . '" title="' . htmlspecialchars($value['title']) . '" description="' . htmlspecialchars($value['description']) . '"/>', "\n";
        }

        echo '</node>', "\n";
    }
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