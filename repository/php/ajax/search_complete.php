<?php
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\Session;
use common\libraries\AndCondition;
use common\libraries\ObjectTableOrder;
use common\libraries\OrCondition;
use common\libraries\PatternMatchCondition;
/**
 * $Id: search_complete.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.ajax
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';

$html = array();

if (Authentication :: is_valid())
{
    $query = Request :: post('query');

    $conditions = array();
    //	$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $this->get_parent_id());
    $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id());
    $or_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
    $or_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
    $conditions[] = new OrCondition($or_conditions);
    $condition = new AndCondition($conditions);

    $objects = RepositoryDataManager :: get_instance()->retrieve_content_objects($condition, array(new ObjectTableOrder(ContentObject :: PROPERTY_TITLE)));

    $html[] = '<ul>';
    while ($object = $objects->next_result())
    {
        $html[] = '<li>' . $object->get_title() . '</li>';
    }
    $html[] = '</ul>';
}
else
{
    $html[] = '<ul>';
    $html[] = '</ul>';
}

echo implode("\n", $html);
?>