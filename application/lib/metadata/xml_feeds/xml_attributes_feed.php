<?php
//require_once dirname(__FILE) . '/../../../../common/database/data_class.class.php';
//require_once dirname(__FILE) . '/../metadata_property_type.class.php';
//require_once dirname(__FILE) . '/../metadata_property_attribute_type.class.php';
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once dirname(__FILE__) . '/../metadata_data_manager.class.php';



$mdm = MetadataDataManager :: get_instance();
$attributes = $mdm->retrieve_metadata_property_attribute_types();
$attributeArray = array();

while($attribute = $attributes->next_result())
{
    $attributeArray[$attribute->get_id()] = $attribute;
}

$properties = $mdm->retrieve_metadata_property_types();

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>', "\n", '<tree>', "\n";
dump_attribute_tree($attributeArray);
dump_property_tree($properties);
echo '</tree>';
function dump_attribute_tree($attributes)
{
    echo '<node id="metadata_property_attribute_type" classes="category unlinked" title="attributes">', "\n";
    foreach($attributes as $id => $attribute)
    {
       $prefix = (!is_null($attribute->get_ns_prefix())) ? $attribute->get_ns_prefix() . ':' : '';
       $name = $attribute->get_name();
       
       echo '<leaf id="attributes_'.$attribute->get_id().'" classes="type type_cda_language" title="' . $prefix . $name. '" description=""/>' . "\n";
    }
    echo '</node>', "\n";
}

function dump_property_tree($properties)
{
    echo '<node id="metadata_property_attribute_type" classes="category unlinked" title="properties">', "\n";
    while($property = $properties->next_result())
    {
       echo '<leaf id="properties_'.$property->get_id().'" classes="type type_cda_language" title="' . $property->get_ns_prefix() . ':' . $property->get_name() . '" description=""/>' . "\n";
    }
    echo '</node>', "\n";
}

function display_attribute($attribute)
{
    //$ns_prefix = (empty($attribute->get_ns_prefix())) ? '' : $attribute->get_ns_prefix() . ':';
    return $ns_prefix . $attribute->get_value();
}
?>
