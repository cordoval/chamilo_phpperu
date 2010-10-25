<?php 
namespace application\metadata;
require_once dirname(__FILE__) . '/../../../../../common/global.inc.php';
require_once dirname(__FILE_) . '/../../metadata_data_manager.class.php';
require_once Path ::get_application_path() . 'lib/metadata/metadata_default_value.class.php';

$condition1 = new EqualityCondition(MetadataDefaultValue :: PROPERTY_PROPERTY_TYPE_ID, Request :: post('property_type_id'));
$condition2 = new EqualityCondition(MetadataDefaultValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID);
$condition = new AndCondition($condition1, $condition2);

$default_values = MetadataDataManager :: get_instance()->retrieve_metadata_default_values($condition);

while($default_value = $default_values->next_result())
{
    $defaults[] = $default_value->get_value();
}
echo json_encode($defaults);
?>
