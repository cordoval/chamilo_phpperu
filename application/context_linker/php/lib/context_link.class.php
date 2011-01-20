<?php
namespace application\context_linker;
use common\libraries\DataClass;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\Translation;

/**
 * This class describes a ContextLink data object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLink extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * ContextLink properties
	 */
        const PROPERTY_ID = 'clid';
	const PROPERTY_ORIGINAL_CONTENT_OBJECT_ID = 'original_content_object_id';
	const PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID = 'alternative_content_object_id';
	const PROPERTY_METADATA_PROPERTY_VALUE_ID = 'metadata_property_value_id';
	const PROPERTY_DATE = 'date';

         /**
         * Returns the id of this data class
         * @return int The id.
         */
        function get_id()
        {
            return $this->get_default_property(self :: PROPERTY_ID);
        }

        /**
         * Sets id of the data class
         * @param int $id
         */
        function set_id($id)
        {
            if (isset($id) && strlen($id) > 0)
            {
                $this->set_default_property(self :: PROPERTY_ID, $id);
            }
        }

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names($extended_property_names = array())
	{
            return array (self :: PROPERTY_ID, self :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, self :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID, self :: PROPERTY_METADATA_PROPERTY_VALUE_ID, self :: PROPERTY_DATE);
	}

	function get_data_manager()
	{
            return ContextLinkerDataManager :: get_instance();
	}

	/**
	 * Returns the original_content_object_id of this ContextLink.
	 * @return the original_content_object_id.
	 */
	function get_original_content_object_id()
	{
            return $this->get_default_property(self :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID);
	}

	/**
	 * Sets the original_content_object_id of this ContextLink.
	 * @param original_content_object_id
	 */
	function set_original_content_object_id($original_content_object_id)
	{
            $this->set_default_property(self :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, $original_content_object_id);
	}

	/**
	 * Returns the alternative_content_object_id of this ContextLink.
	 * @return the alternative_content_object_id.
	 */
	function get_alternative_content_object_id()
	{
            return $this->get_default_property(self :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID);
	}

	/**
	 * Sets the alternative_content_object_id of this ContextLink.
	 * @param alternative_content_object_id
	 */
	function set_alternative_content_object_id($alternative_content_object_id)
	{
            $this->set_default_property(self :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID, $alternative_content_object_id);
	}

	/**
	 * Returns the metadata_property_value_id of this ContextLink.
	 * @return the metadata_property_value_id.
	 */
	function get_metadata_property_value_id()
	{
            return $this->get_default_property(self :: PROPERTY_METADATA_PROPERTY_VALUE_ID);
	}

	/**
	 * Sets the metadata_property_value_id of this ContextLink.
	 * @param metadata_property_value_id
	 */
	function set_metadata_property_value_id($metadata_property_value_id)
	{
            $this->set_default_property(self :: PROPERTY_METADATA_PROPERTY_VALUE_ID, $metadata_property_value_id);
	}

	/**
	 * Returns the date of this ContextLink.
	 * @return the date.
	 */
	function get_date()
	{
            return $this->get_default_property(self :: PROPERTY_DATE);
	}

	/**
	 * Sets the date of this ContextLink.
	 * @param date
	 */
	function set_date($date)
	{
            $this->set_default_property(self :: PROPERTY_DATE, $date);
	}


	static function get_table_name()
	{
            return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
	}

        function create()
        {
            //endless recursion prevention
            //retrieve all linked content objects and look if alternative_content_object_id is linked in any way
            $condition = new EqualityCondition(ContextLink :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID, $this->get_original_content_object_id());
            $full_context_links = $this->get_data_manager()->retrieve_full_context_links_recursive($condition);

            foreach($full_context_links as $n => $full_context_link)
            {
                if(($full_context_link[ContextLinkerManager :: PROPERTY_ORIG_ID] == $this->get_alternative_content_object_id()) || $full_context_link[ContextLinkerManager :: PROPERTY_ALT_ID] == $this->get_alternative_content_object_id())
                {
                    $this->add_error(Translation :: get('AlreadyInChain'));
                    return false;
                }
            }
            return parent :: create();
        }

        
}

?>