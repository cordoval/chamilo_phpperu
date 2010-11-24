<?php 
namespace application\metadata;

use common\libraries\Path;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\Request;

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once dirname(__FILE_) . '/../lib/metadata_data_manager.class.php';
require_once Path ::get_application_path() . 'metadata/php/lib/metadata_default_value.class.php';

$condition1 = new EqualityCondition(MetadataDefaultValue :: PROPERTY_PROPERTY_TYPE_ID, Request :: post('property_type_id'));
$condition2 = new EqualityCondition(MetadataDefaultValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID);
$condition = new AndCondition($condition1, $condition2);

$default_values = MetadataDataManager :: get_instance()->retrieve_metadata_default_values($condition);
$i = 0;
$defaults =array();
while($default_value = $default_values->next_result())
{
    $defaults[] = $default_value->get_value();
    $i++;
}
echo json_encode($defaults);
?>
