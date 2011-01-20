<?php 
namespace application\metadata;
use common\libraries\DataClass;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\OrCondition;
use common\libraries\Translation;
/**
 * This class describes a MetadataPropertyType data object
 * @author Jens Vanderheyden
 */
class MetadataPropertyType extends DataClass
{
    const CLASS_NAME = __CLASS__;

    /**
     * MetadataPropertyType properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_NAMESPACE = 'namespace';
    const PROPERTY_NS_PREFIX = 'ns_prefix';
    const PROPERTY_NAME = 'name';

    private $ns_prefix = false;
    
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
            return array (self :: PROPERTY_ID, self :: PROPERTY_NAMESPACE, self :: PROPERTY_NAME);
    }

    function get_data_manager()
    {
            return MetadataDataManager :: get_instance();
    }

    /**
     * Returns the id of this MetadataPropertyType.
     * @return the id.
     */
    function get_id()
    {
            return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the id of this MetadataPropertyType.
     * @param id
     */
    function set_id($id)
    {
            $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Returns the ns_prefix of this MetadataPropertyType.
     * @return the namespace_id.
     */
    function get_namespace()
    {
            return $this->get_default_property(self :: PROPERTY_NAMESPACE);
    }

    /**
     * Sets the ns_prefix of this MetadataPropertyType.
     * @param namespace
     */
    function set_namespace($namespace)
    {
            $this->set_default_property(self :: PROPERTY_NAMESPACE, $namespace);
    }

    /**
     * Returns the ns_prefix of this MetadataPropertyType.
     * @return the ns_prefix.
     */
    function get_ns_prefix()
    {
        if(!$this->ns_prefix)
        {
            $namespace = $this->get_data_manager()->retrieve_metadata_namespace($this->get_namespace());

            if(!$namespace)
            {
                return false;
            }
            $this->set_ns_prefix($namespace->get_ns_prefix());
        }
        return $this->ns_prefix;
    }

    /**
     * Sets the ns_prefix of this MetadataPropertyType.
     * @param ns_prefix
     */
    function set_ns_prefix($ns_prefix)
    {
            $this->ns_prefix = $ns_prefix;
    }

    /**
     * Returns the value of this MetadataPropertyType.
     * @return the value.
     */
    function get_name()
    {
            return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the value of this MetadataPropertyType.
     * @param value
     */
    function set_name($name)
    {
            $this->set_default_property(self :: PROPERTY_NAME, $name);
    }


    static function get_table_name()
    {
            return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    }


    /*
     * object creation if name + ns_prfix are unique
     * return MetadaPropertyType or false
     */
    function create()
    {
        $condition1 = new EqualityCondition(MetadataPropertyType :: PROPERTY_NAME, $this->get_name());
        $condition2 = new EqualityCondition(MetadataPropertyType :: PROPERTY_NAMESPACE, $this->get_namespace());

        $condition =  new AndCondition($condition1, $condition2);

        if($this->get_data_manager()->count_metadata_property_types($condition) >= 1)
        {
           $this->add_error(Translation :: get('ObjectAlreadyExists'));
           return false;
        }
        return parent :: create();
    }

    function delete()
    {
        //check dependencies before deleting
        //if any property_values delete is not possible
        $mdm = $this->get_data_manager();

        $condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID, $this->get_id());

        $count = $mdm->count_content_object_metadata_property_values($condition);
        $count += $mdm->count_user_metadata_property_values($condition);

        if($count === 0)
        {
            if(parent :: delete())
            {
                //delete associations
                $condition1 =  new EqualityCondition(MetadataPropertyNesting :: PROPERTY_PARENT_ID, $this->get_id());

                $condition2 =  new EqualityCondition(MetadataPropertyNesting :: PROPERTY_CHILD_ID, $this->get_id());
                $condition3 = new EqualityCondition(MetadataPropertyNesting :: PROPERTY_CHILD_TYPE, Utilities :: camelcase_to_underscores(self :: CLASS_NAME));
                $condition4 = new AndCondition($condition2, $condition3);

                $condition = new OrCondition($condition1, $condition4);

                $metadata_property_nestings = $mdm->retrieve_metadata_property_nestings($condition);

                while($metadata_property_nesting = $metadata_property_nestings->next_result())
                {
                    $metadata_property_nesting->delete();
                }
                
                $condition1 =  new EqualityCondition(MetadataAttributeNesting :: PROPERTY_PARENT_ID, $this->get_id());

                $condition2 =  new EqualityCondition(MetadataAttributeNesting :: PROPERTY_CHILD_ID, $this->get_id());
                $condition3 = new EqualityCondition(MetadataAttributeNesting :: PROPERTY_CHILD_TYPE, Utilities :: camelcase_to_underscores(self :: CLASS_NAME));
                $condition4 = new AndCondition($condition2, $condition3);

                $condition = new OrCondition($condition1, $condition4);

                $metadata_attribute_nestings = $mdm->retrieve_metadata_attribute_nestings($condition);

                while($metadata_attribute_nesting = $metadata_attribute_nestings->next_result())
                {
                    $metadata_attribute_nesting->delete();
                }

                //content_object_property_metadata
                $condition =  new EqualityCondition(ContentObjectPropertyMetadata :: PROPERTY_PROPERTY_TYPE_ID, $this->get_id());
                $content_object_property_metadatas =  $mdm->retrieve_content_object_property_metadatas($condition);

                while($content_object_property_metadata = $content_object_property_metadatas->next_result())
                {
                    $content_object_property_metadata->delete();
                }

                //default_values
                $condition = new EqualityCondition(MetadataDefaultValue :: PROPERTY_PROPERTY_TYPE_ID, $this->get_id());
                $metadata_default_values = $mdm->retrieve_metadata_default_values($condition);

                while($metadata_default_value = $metadata_default_values->next_result())
                {
                    $metadata_default_value->delete();
                }
            }
        }
        else
        {
            $this->add_error(Translation :: get('ChildrenExist'));
            return false;
        }
        return true;
    }
    /*
     * @return string ns_prefix + name
     */
    function render_name()
    {
        $pref = $this->get_ns_prefix();
        $prefix = (empty($pref)) ? '' : $this->get_ns_prefix() . ':';
        return $prefix . $this->get_name();
    }
}

?>