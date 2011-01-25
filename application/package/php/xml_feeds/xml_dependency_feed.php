<?php
namespace application\package;

use common\libraries\OrCondition;
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
    
    $query_condition = Utilities :: query_to_condition($_GET['query'], array(Dependency :: PROPERTY_NAME));
    if (isset($query_condition))
    {
        $conditions[] = $query_condition;
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
    $dependencies = $udm->retrieve_dependencies($condition, null, null, array(
            new ObjectTableOrder(Dependency :: PROPERTY_NAME)));
            
}
else
{
    $dependencies = null;
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<tree>', "\n";

if (isset($dependencies))
{
    dump_tree($dependencies);
}

echo '</tree>';

function dump_tree($dependencies)
{
    if (isset($dependencies) && $dependencies->size() == 0)
    {
        return;
    }
    
    echo '<node id="0" classes="category unlinked" title="' . Translation :: get('Dependencies') . '">' . "\n";
    
    while ($dependency = $dependencies->next_result())
    {
        echo '<leaf id="dependency_' . $dependency->get_id() . '" classes="type type_dependency" title="' . htmlspecialchars($dependency->get_name()) . '" description="' . htmlspecialchars($dependency->get_name()) . '"/>' . "\n";
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