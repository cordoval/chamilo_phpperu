<?php
/**
 * @package common.xml_feeds
 * @author Nick Van Loocke
 */
require_once dirname(__FILE__) . '/../global.inc.php';
require_once dirname(__FILE__) . '/../../application/lib/cba/cba_data_manager.class.php';

if (Authentication :: is_valid())
{
    $query = Request :: get('query');
    $exclude = Request :: get('exclude');

	$indicator_conditions = array();

    if ($query)
    {
        $q = '*' . $query . '*';

        $indicator_condition[] = new OrCondition(array(new PatternMatchCondition(Indicator :: PROPERTY_TITLE, $q), new PatternMatchCondition(Indicator :: PROPERTY_DESCRIPTION, $q)));
	}

    if ($exclude)
    {
        if (! is_array($exclude))
        {
            $exclude = array($exclude);
        }

        $exclude_conditions = array();
        $exclude_condition['indicator'] = array();
        
        foreach ($exclude as $id)
        {
            $id = explode('_', $id);

            if($id[0] == 'indicator')
            {
            	$condition = new NotCondition(new EqualityCondition(Indicator :: PROPERTY_ID, $id[1]));
            }

            $exclude_conditions[$id[0]][] = $condition;
        }

        if (count($exclude_conditions['indicator']) > 0)
        {
      		$indicator_conditions[] = new AndCondition($exclude_conditions['indicator']);
        }

    }

    if (count($indicator_conditions) > 0)
    {
        $indicator_condition = new AndCondition($indicator_conditions);
    }
    else
    {
        $indicator_condition = null;
    }


    $udm = CbaDataManager :: get_instance();
    $indicator_result_set = $udm->retrieve_indicators($indicator_condition, null, null, array(new ObjectTableOrder(Indicator :: PROPERTY_ID)));

	$indicators = array();
    while ($indicator = $indicator_result_set->next_result())
    {
        $indicators[] = $indicator;
    }
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>', "\n", '<tree>', "\n";

dump_tree($indicators);

echo '</tree>';
function dump_tree($indicator)
{
	echo '<node id="indicator" classes="category unlinked" title="Indicators">', "\n";
	foreach($indicator as $key => $value)
	{
		echo '<leaf id="indicator_'.$key.'" classes="type type_cda_language" title="'.$indicator[$key]->get_title().'" description=""/>' . "\n";	
	}
	echo '</node>', "\n";
}

?>