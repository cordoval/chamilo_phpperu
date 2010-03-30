<?php
/**
 * $Id: xml_role_feed.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.xml_feeds
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';

Translation :: set_application('rights');

if (Authentication :: is_valid())
{
    $conditions = array();

    $query_condition = Utilities :: query_to_condition($_GET['query'], array(RightsTemplate :: PROPERTY_NAME, RightsTemplate :: PROPERTY_DESCRIPTION));
    if (isset($query_condition))
    {
        $conditions[] = $query_condition;
    }

    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $c[] = new EqualityCondition(RightsTemplate :: PROPERTY_ID, $id);
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

    $rdm = RightsDataManager :: get_instance();
    $rights_templates = $rdm->retrieve_rights_templates($condition);
}
else
{
    $rights_templates = null;
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

if (isset($rights_templates))
{
    dump_tree($rights_templates);
}

echo '</tree>';

function dump_tree($rights_templates)
{
    if (isset($rights_templates) && $rights_templates->size() == 0)
    {
        return;
    }

    echo '<node id="0" classes="category unlinked" title="', Translation :: get('RightsTemplates'), '">', "\n";

    while ($rights_template = $rights_templates->next_result())
    {
        $value = RightsUtilities :: rights_template_for_element_finder($rights_template);
        echo '<leaf id="', $rights_template->get_id(), '" classes="', $value['class'], '" title="', htmlentities($value['title']), '" description="', htmlentities(isset($value['description']) && ! empty($value['description']) ? $value['description'] : $value['title']), '"/>', "\n";
    }

    echo '</node>', "\n";
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