<?php 
/**
 * metadata
 */

/**
 * This class describes a MetadataPropertyAttributeType data object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataPropertyAttributeType extends DataClass
{
    const CLASS_NAME = __CLASS__;

    /**
     * MetadataPropertyAttributeType properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_NS_PREFIX = 'ns_prefix';
    const PROPERTY_NAME = 'name';


    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
            return array (self :: PROPERTY_ID, self :: PROPERTY_NS_PREFIX, self :: PROPERTY_NAME, self :: PROPERTY_VALUE, self :: PROPERTY_VALUE_TYPE);
    }

    function get_data_manager()
    {
            return MetadataDataManager :: get_instance();
    }

    /**
     * Returns the id of this MetadataPropertyAttributeType.
     * @return the id.
     */
    function get_id()
    {
            return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the id of this MetadataPropertyAttributeType.
     * @param id
     */
    function set_id($id)
    {
            $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Returns the ns_prefix of this MetadataPropertyAttributeType.
     * @return the ns_prefix.
     */
    function get_ns_prefix()
    {
            return $this->get_default_property(self :: PROPERTY_NS_PREFIX);
    }

    /**
     * Sets the ns_prefix of this MetadataPropertyAttributeType.
     * @param ns_prefix
     */
    function set_ns_prefix($ns_prefix)
    {
            $this->set_default_property(self :: PROPERTY_NS_PREFIX, $ns_prefix);
    }

    /**
     * Returns the name of this MetadataPropertyAttributeType.
     * @return the name.
     */
    function get_name()
    {
            return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this MetadataPropertyAttributeType.
     * @param name
     */
    function set_name($name)
    {
            $this->set_default_property(self :: PROPERTY_NAME, $name);
    }




    static function get_table_name()
    {
            return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function render_name()
    {
        $pref = $this->get_ns_prefix();
        $prefix = (empty($pref)) ? '' : $this->get_ns_prefix() . ':';
        return $prefix . $this->get_name();
    }

    function delete()
    {
        //check dependencies before deleting
        $mdm = $this->get_data_manager();

        $condition = new EqualityCondition(MetadataPropertyAttributeValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID, $this->get_id());
        $count = $mdm->count_metadata_property_attribute_types($condition);

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

                $metadata_property_nestings = $mdm->retrieve_property_nestings($condition);

                while($metadata_property_nesting = $metadata_property_nestings->next_result())
                {
                    $metadata_property_nesting->delete();
                }

                $condition1 =  new EqualityCondition(MetadataAttributeNesting :: PROPERTY_PARENT_ID, $this->get_id());

                $condition2 =  new EqualityCondition(MetadataAttributeNesting :: PROPERTY_CHILD_ID, $this->get_id());
                $condition3 = new EqualityCondition(MetadataAttributeNesting :: PROPERTY_CHILD_TYPE, Utilities :: camelcase_to_underscores(self :: CLASS_NAME));
                $condition4 = new AndCondition($condition2, $condition3);

                $condition = new OrCondition($condition1, $condition4);

                $metadata_attribute_nestings = $mdm->retrieve_Attribute_nestings($condition);

                while($metadata_attribute_nesting = $metadata_attribute_nestings->next_result())
                {
                    $metadata_attribute_nesting->delete();
                }
            }
        }
    }
}

?>