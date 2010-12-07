<?php
namespace application\metadata;

use common\libraries\DataClass;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\Translation;
use common\libraries\AndCondition;

use user\UserDataManager;

use repository\ContentObject;

/**
 * This class describes a ContentObjectPropertyMetadata data object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContentObjectPropertyMetadata extends DataClass
{
    const CLASS_NAME = __CLASS__;

    /**
     * ContentObjectPropertyMetadata properties
     */
    const PROPERTY_PROPERTY_TYPE_ID = 'property_type_id';
    const PROPERTY_CONTENT_OBJECT_PROPERTY = 'content_object_property';
    const PROPERTY_SOURCE = 'source';

    const SOURCE_TEXT = '1';
    const SOURCE_CHAMILO_USER = '2';
    const SOURCE_TIMESTAMP = '3';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
            return array (self :: PROPERTY_ID, self :: PROPERTY_PROPERTY_TYPE_ID, self :: PROPERTY_CONTENT_OBJECT_PROPERTY, self :: PROPERTY_SOURCE);
    }

    function get_data_manager()
    {
            return MetadataDataManager :: get_instance();
    }

    /**
     * Returns the property_type_id of this ContentObjectPropertyMetadata.
     * @return the property_type_id.
     */
    function get_property_type_id()
    {
            return $this->get_default_property(self :: PROPERTY_PROPERTY_TYPE_ID);
    }

    /**
     * Sets the property_type_id of this ContentObjectPropertyMetadata.
     * @param property_type_id
     */
    function set_property_type_id($property_type_id)
    {
            $this->set_default_property(self :: PROPERTY_PROPERTY_TYPE_ID, $property_type_id);
    }

    /**
     * Returns the content_object_property of this ContentObjectPropertyMetadata.
     * @return the content_object_property.
     */
    function get_content_object_property()
    {
            return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_PROPERTY);
    }

    /**
     * Sets the content_object_property of this ContentObjectPropertyMetadata.
     * @param content_object_property
     */
    function set_content_object_property($content_object_property)
    {
            $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_PROPERTY, $content_object_property);
    }

    function get_source()
    {
            return $this->get_default_property(self :: PROPERTY_SOURCE);
    }

    function set_source($source)
    {
            $this->set_default_property(self :: PROPERTY_SOURCE, $source);
    }


    static function get_table_name()
    {
            return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    }

    function format_content_object_property_according_to_source($content_object)
    {
        $function_name = 'get_' . $this->get_content_object_property();
        if(method_exists(ContentObject :: CLASS_NAME, $function_name))
        {
            $content_object_property = $content_object->$function_name();
            switch($this->get_source())
            {
                case self :: SOURCE_TEXT:
                    return $content_object_property;
                    break;

                case self :: SOURCE_CHAMILO_USER:
                    $udm = UserDataManager :: get_instance();
                    $user = $udm->retrieve_user($content_object_property);
                    return $user->get_firstname() . ' ' . $user->get_lastname();
                    break;

                case self :: SOURCE_TIMESTAMP:
                    return date('Ymd', $content_object_property);
                    break;
            }
        }
    }

    /*
     * only if content object property is unique
     */
    function create()
    {
        $condition = new EqualityCondition(ContentObjectPropertyMetadata :: PROPERTY_CONTENT_OBJECT_PROPERTY, $this->get_content_object_property());
        $count = $this->get_data_manager()->count_content_object_property_metadatas($condition);

        if($count >= 1)
        {
            $this->add_error(Translation :: get('ObjectAlreadyExists', array('OBJECT' => Translation :: get('ContentObjectPropertyMetadata')), Utilities :: COMMON_LIBRARIES));
            return false;
        }
        return parent :: create();
    }

    /*
     * also deletes linked property attribute values
     */
    function delete()
    {
        $condition1 = new EqualityCondition(MetadataPropertyAttributeValue :: PROPERTY_PARENT_ID, $this->get_id());
        $condition2 = new EqualityCondition(MetadataPropertyAttributeValue :: PROPERTY_RELATION, MetadataPropertyAttributeValue :: RELATION_CONTENT_OBJECT_PROPERTY);

        $condition = new AndCondition($condition1, $condition2);

        $mdm = MetadataManager :: get_instance();
        $metadata_property_attribute_values = $mdm->retrieve_metadata_property_attribute_values();

        $fails = 0;
        while($metadata_property_attribute_value = $metadata_property_attribute_values->next_result())
        {
            if($metadata_property_attribute_value->delete())
            {
                $fails ++;
            }
        }

        if($fails == 0) return parent :: delete();

        $this->add_error(Translation :: get('ObjectsNotDeleted', array('OBJECT' => Translation :: get('MetadataPropertyAttributeValue')), Utilities :: COMMON_LIBRARIES));
        return false;
    }
}

?>