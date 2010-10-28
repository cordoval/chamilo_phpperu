<?php
namespace application\metadata;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\ResourceManager;
use common\libraries\Path;

class MetadataForm extends FormValidator
{
    private $property_types = array();
    
    function MetadataForm($name, $method, $action)
    {
        $this->retrieve_property_types();

        parent :: __construct($name, $method, $action);
    }

    function retrieve_property_types()
    {
        $mdm = MetadataDataManager :: get_instance();
        $property_types = $mdm->retrieve_metadata_property_types();

        while($property_type = $property_types->next_result())
        {
            $this->property_types[$property_type->get_id()] = $property_type->get_ns_prefix() .':'. $property_type->get_name();
        }
    }

    function get_property_types()
    {
        return $this->property_types;
    }

    function build_empty_property_value()
    {
        $group = array();

        $group[] = $this->createElement('select', MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID, Translation :: get('PropertyType'), $this->property_types);
        $group[] = $this->createElement('text', MetadataPropertyValue :: PROPERTY_VALUE, Translation :: get('PropertyValue'), array('id' => MetadataManager :: PARAM_METADATA_PROPERTY_VALUE));

        $this->addGroup($group, '', Translation :: get('NewPropertyValue'));

        //javascript
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'application/metadata/resources/javascript/set_metadata_defaults.js'));
    }

    function create_metadata_property_value()
    {
        $values = $this->exportValues();

        $metadata_property_value = new MetadataPropertyValue();

        $metadata_property_value->set_content_object_id($values[MetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID]);
        $metadata_property_value->set_property_type_id($values[MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID]);
        $metadata_property_value->set_value($values[MetadataPropertyValue :: PROPERTY_VALUE]);

        return $metadata_property_value->create();
    }
}

?>
