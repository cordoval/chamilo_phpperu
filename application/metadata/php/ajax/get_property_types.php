<?php
namespace application\metadata;

use common\libraries\Path;
use common\libraries\Request;
use common\libraries\EqualityCondition;

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once dirname(__FILE_) . '/../lib/metadata_data_manager.class.php';
require_once Path ::get_application_path() . 'metadata/php/lib/metadata_property_attribute_type.class.php';

$mdm = MetadataDataManager :: get_instance();

$condition= new EqualityCondition(MetadataPropertyType :: PROPERTY_NAMESPACE, Request :: post(MetadataPropertyType :: PROPERTY_ID));

$property_types = $mdm->retrieve_metadata_property_types($condition);

$types= array();

while($property_type = $property_types->next_result())
{
    $types[$property_type->get_id()] = $property_type->get_name();
}

echo json_encode($types);
?>
