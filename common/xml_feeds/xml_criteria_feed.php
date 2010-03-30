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

	$criteria_conditions = array();

    if ($query)
    {
        $q = '*' . $query . '*';

        $criteria_condition[] = new OrCondition(array(new PatternMatchCondition(Criteria :: PROPERTY_TITLE, $q), new PatternMatchCondition(Criteria :: PROPERTY_DESCRIPTION, $q)));
	}

    if ($exclude)
    {
        if (! is_array($exclude))
        {
            $exclude = array($exclude);
        }

        $exclude_conditions = array();
        $exclude_condition['criteria'] = array();
        
        foreach ($exclude as $id)
        {
            $id = explode('_', $id);

            if($id[0] == 'criteria')
            {
            	$condition = new NotCondition(new EqualityCondition(Criteria :: PROPERTY_ID, $id[1]));
            }

            $exclude_conditions[$id[0]][] = $condition;
        }

        if (count($exclude_conditions['criteria']) > 0)
        {
      		$criteria_conditions[] = new AndCondition($exclude_conditions['criteria']);
        }

    }

    if (count($criteria_conditions) > 0)
    {
        $criteria_condition = new AndCondition($criteria_conditions);
    }
    else
    {
        $criteria_condition = null;
    }


    $udm = CbaDataManager :: get_instance();
    $criteria_result_set = $udm->retrieve_criterias($criteria_condition, null, null, array(new ObjectTableOrder(Criteria :: PROPERTY_ID)));

	$criterias = array();
    while ($criteria = $criteria_result_set->next_result())
    {
        $criterias[] = $criteria;
    }
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>', "\n", '<tree>', "\n";

dump_tree($criterias);

echo '</tree>';
function dump_tree($criteria)
{
	echo '<node id="criteria" classes="category unlinked" title="Criteria">', "\n";
	foreach($criteria as $key => $value)
	{
		echo '<leaf id="criteria_'.$criteria[$key]->get_id().'" classes="type type_cda_language" title="'.$criteria[$key]->get_title().'" description=""/>' . "\n";	
	}
	echo '</node>', "\n";
}

?>