<?php
namespace application\package;


use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\NotCondition;
use common\libraries\Authentication;
use common\libraries\ObjectTableOrder;
use common\libraries\Utilities;
use common\libraries\Translation;
 
 
/**
 * $Id: xml_package_feed.php 224 2009-11-13 14:40:30Z kariboe $
 * @package author.xml_feeds
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';

if (Authentication :: is_valid())
{
    $conditions = array();

    $query_condition = Utilities :: query_to_condition($_GET['query'], array(Author :: PROPERTY_NAME));
    if (isset($query_condition))
    {
        $conditions[] = $query_condition;
    }

    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $c[] = new EqualityCondition(Author :: PROPERTY_ID, $id);
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

    $udm = PackageDataManager :: get_instance();
    $authors = $udm->retrieve_authors($condition, null, null, array(new ObjectTableOrder(Author :: PROPERTY_NAME)));
}
else
{
    $authors = null;
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<tree>', "\n";

if (isset($authors))
{
    dump_tree($authors);
}

echo '</tree>';

function dump_tree($authors)
{
    if (isset($authors) && $authors->size() == 0)
    {
        return;
    }

    echo '<node id="0" classes="category unlinked" title="' . Translation :: get('Authors') . '">' . "\n";

    while ($author = $authors->next_result())
    {
        echo '<leaf id="author_' . $author->get_id() . '" classes="type type_author" title="' . htmlspecialchars($author->get_name()) . '" description="' . htmlspecialchars($author->get_name()) . '"/>' . "\n";
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