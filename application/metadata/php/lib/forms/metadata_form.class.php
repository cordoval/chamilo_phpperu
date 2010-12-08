<?php
namespace application\metadata;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\ResourceManager;
use common\libraries\Path;
use common\libraries\Utilities;

class MetadataForm extends FormValidator
{
    private $property_types = array();
    private $parent_type;

    const PARENT_USER = 'user';
    const PARENT_CONTENT_OBJECT = 'content_object';
    const PARENT_ID = 'parent_id';

    function __construct($name, $method, $action)
    {
        $this->retrieve_property_types();

        parent :: __construct($name, $method, $action);
    }

    //sets the type of metadata to edit
    function set_parent_type($parent_type)
    {
        $this->parent_type = $parent_type;
    }
    //sets...
    function get_parent_type()
    {
        return $this->parent_type;
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

    function retrieve_prefixes()
    {
        $mdm = MetadataDataManager :: get_instance();

        $prefixes = $mdm->retrieve_prefixes();
    }

    function get_property_types()
    {
        return $this->property_types;
    }

    function build_empty_property_value()
    {
        $group = array();

        $group[] = $this->createElement('select', MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID, Translation :: get('MetadataPropertyType'), $this->property_types);
        $group[] = $this->createElement('text', MetadataPropertyValue :: PROPERTY_VALUE, Translation :: get('MetadataPropertyValue'), array('id' => MetadataManager :: PARAM_METADATA_PROPERTY_VALUE));

        $this->addGroup($group, '', Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES));

        //javascript
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'application/metadata/resources/javascript/set_metadata_defaults.js'));
    }

    /*
     * creates content object property value or user property value depending on $this->parent_type (user or content_object)
     * @return MetadataPropertyValue or false
     */
    function create_metadata_property_value()
    {
        $values = $this->exportValues();

        $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($this->parent_type) . 'MetadataPropertyValue';

        require_once dirname(__FILE__) . '/../' . $this->parent_type . '_metadata_property_value.class.php';

        $metadata_property_value = new $class();
        
//        if($this->parent_type == self :: PARENT_CONTENT_OBJECT)
//        {
//            $metadata_property_value->set_content_object_id($values[ContentObjectMetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID]);
//        }
//        elseif($this->parent_type == self :: PARENT_USER)
//        {
//            $metadata_property_value->set_user_id($values[UserMetadataPropertyValue :: PROPERTY_USER_ID]);
//        }

        $function = 'set_' . $this->get_parent_type() . '_id';

        $metadata_property_value->$function($values[self :: PARENT_ID]);
        
        $metadata_property_value->set_property_type_id($values[MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID]);
        $metadata_property_value->set_value($values[MetadataPropertyValue :: PROPERTY_VALUE]);

        if($metadata_property_value->create())
        {
            return $metadata_property_value;
        }
        return false;
    }
}

?>
