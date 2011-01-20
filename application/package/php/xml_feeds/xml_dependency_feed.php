<?php
namespace application\package;

use common\libraries;

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
        
    $query = Request :: get('query');
    $exclude = Request :: get('exclude');
    
    $package_conditions = array();
    
    if ($query)
    {
        $q = '*' . $query . '*';
        $package_conditions[] = new PatternMatchCondition(Package :: PROPERTY_NAME, $q);
    }
    
    if ($exclude)
    {
        if (! is_array($exclude))
        {
            $exclude = array($exclude);
        }
        
        $exclude_conditions = array();
        $exclude_conditions['package'] = array();
        
        foreach ($exclude as $id)
        {
            $id = explode('_', $id);
            
            if ($id[0] == 'package')
            {
                $condition = new NotCondition(new EqualityCondition(Package :: PROPERTY_ID, $id[1]));
            }
            
            $exclude_conditions[$id[0]][] = $condition;
        }
        
        if (count($exclude_conditions['package']) > 0)
        {
            $package_conditions[] = new AndCondition($exclude_conditions['package']);
        }
    }
    
    $package_condition = null;
    if (count($package_conditions) > 1)
    {
        $package_condition = new AndCondition($package_conditions);
    }
    elseif (count($package_conditions) == 1)
    {
        $package_condition = $package_conditions[0];
    }
    
    $packages = array();
    
    $package_result_set = PackageDataManager :: get_instance()->retrieve_packages($package_condition, null, null, array(
            new ObjectTableOrder(Package :: PROPERTY_NAME)));
    
    while ($package = $package_result_set->next_result())
    {
        $packages[$package->get_section()][$package->get_category()][] = $package;
    
    }
}
else
{
    $dependencies = null;
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<tree>', "\n";

if (isset($packages))
{
    dump_packages_tree($packages);
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
        echo '<leaf id="dependency_' . $dependency->get_id() . '" classes="type type_dependency" title="' . htmlspecialchars($dependency->get_id_dependency() . ' ' . $dependency->get_version()) . '" description="' . htmlspecialchars($dependency->get_id()) . '"/>' . "\n";
    }
    
    echo '</node>' . "\n";
}

function dump_packages_tree($result)
{
    foreach ($result as $section => $categories)
    {
        echo '<node classes="category" title="' . htmlspecialchars($section) . '" description="' . htmlspecialchars($section) . '">', "\n";
        foreach ($categories as $category => $packages)
        {
            echo '<node classes="category" title="' . htmlspecialchars($category) . '" description="' . htmlspecialchars($category) . '">', "\n";
            foreach ($packages as $package)
            {
                echo '<leaf id="dependency_' . $package->get_id() . '" classes="type type_package" title="' . htmlspecialchars($package->get_name() . ' ' . $package->get_version()) . '" description="' . htmlspecialchars($package->get_name()) . '"/>' . "\n";
            }
            echo '</node>', "\n";
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