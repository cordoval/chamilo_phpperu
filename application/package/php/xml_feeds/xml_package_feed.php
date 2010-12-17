<?php
namespace application\package;

use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\NotCondition;
use common\libraries\Authentication;
use common\libraries\ObjectTableOrder;

/**
 * $Id: xml_package_feed.php 224 2009-11-13 14:40:30Z kariboe $
 * @package package.xml_feeds
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';

if (Authentication :: is_valid())
{
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
        //        $package_parent_id = $package->get_parent();
        //
        //        if (! is_array($packages[$package_parent_id]))
        //        {
        //            $packages[$package_parent_id] = array();
        //        }
        //
        //        if (! isset($packages[$package_parent_id][$package->get_id()]))
        //        {
        //            $packages[$package_parent_id][$package->get_id()] = $package;
        //        }
        //
        //        if ($package_parent_id != 0)
        //        {
        //            $tree_parents = $package->get_parents(false);
        //
        //            while ($tree_parent = $tree_parents->next_result())
        //            {
        //                $tree_parent_parent_id = $tree_parent->get_parent();
        //
        //                if (! is_array($packages[$tree_parent_parent_id]))
        //                {
        //                    $packages[$tree_parent_parent_id] = array();
        //                }
        //
        //                if (! isset($packages[$tree_parent_parent_id][$tree_parent->get_id()]))
        //                {
        //                    $packages[$tree_parent_parent_id][$tree_parent->get_id()] = $tree_parent;
        //                }
        //            }
        //        }
        $packages[$package->get_section()][$package->get_category()][] = $package;
    
    }
    
//    $packages_tree = get_package_tree(0, $packages);
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n", '<tree>' . "\n";
echo dump_packages_tree($packages);
echo '</tree>';

function dump_packages_tree($result)
{
    foreach ($result as $section => $categories)
    {
        echo '<node classes="type type_section" title="' . htmlspecialchars($section) . '" description="' . htmlspecialchars($section) . '">', "\n";
        foreach ($categories as $category => $packages)
        {
            echo '<node classes="type type_category" title="' . htmlspecialchars($category) . '" description="' . htmlspecialchars($category) . '">', "\n";
            foreach ($packages as $package)
            {
                echo '<leaf id="package_' . $package->get_id() . '" classes="type type_package" title="' . htmlspecialchars($package->get_name()) . '" description="' . htmlspecialchars($package->get_name()) . '"/>' . "\n";
            }
            echo '</node>', "\n";
        }
        echo '</node>', "\n";
    }
}
?>