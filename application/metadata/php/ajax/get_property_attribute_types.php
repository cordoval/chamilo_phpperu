<?php
namespace application\metadata;

use common\libraries\Path;

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once dirname(__FILE_) . '/../lib/metadata_data_manager.class.php';
require_once Path ::get_application_path() . 'metadata/php/lib/metadata_property_attribute_type.class.php';

$mdm = MetadataManager :: get_instance();

$property_attribute_types = $mdm->retrieve_metadata_property_attribute_types();

$types= array();

while($property_attribute_type = $property_attribute_types->next_result())
{
    $types[$property_attribute_type->get_id()] = $property_attribute_type->render_name();
}

echo json_encode($types);
?>
