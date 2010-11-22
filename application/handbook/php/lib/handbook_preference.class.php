<?php
namespace application\handbook;
use common\libraries\DataClass;
use common\libraries\Utilities;


/**
 * This class describes a HandbookPreference data object
 * @author Nathalie Blocry
 */
class HandbookPreference extends DataClass
{
	const CLASS_NAME = __CLASS__;
        const TABLE_NAME = 'handbook_preference';

	/**
	 * HandbookPreference properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_HANDBOOK_PUBLICATION_ID = 'handbook_publication_id';
	const PROPERTY_IMPORTANCE = 'importance';
	const PROPERTY_BELONGS_TO = 'belongs_to';
	const PROPERTY_METADATA_PROPERTY_TYPE_ID = 'metadata_property_type_id';
        const PROPERTY_PUB_VALUE= 'pub_value';

	
	static function get_default_property_names()
	{
		return array (self::PROPERTY_ID , self :: PROPERTY_HANDBOOK_ID , self :: PROPERTY_IMPORTANCE , self :: PROPERTY_BELONGS_TO , self :: PROPERTY_METADATA_PROPERTY_TYPE_ID , self :: PROPERTY_PUB_VALUE);

        }

	function get_data_manager()
	{
		return HandbookDataManager :: get_instance();
	}

	
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	
	function get_handbook_publication_id()
	{
		return $this->get_default_property(self :: PROPERTY_HANDBOOK_PUBLICATION_ID);
	}

	
	function set_handbook_publication_id($publication_id)
	{
		$this->set_default_property(self :: PROPERTY_HANDBOOK_PUBLICATION_ID, $publication_id);
	}

	
	function get_importance()
	{
		return $this->get_default_property(self :: PROPERTY_IMPORTANCE);
	}

	
	function set_importance($importance)
	{
		$this->set_default_property(self :: PROPERTY_IMPORTANCE, $importance);
	}

	
	function get_belongs_to()
	{
		return $this->get_default_property(self :: PROPERTY_BELONGS_TO);
	}

	
	function set_belong_to($belong_to)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHER_ID, $publisher_id);
	}

	
	function get_metadata_property_type_id()
	{
		return $this->get_default_property(self :: PROPERTY_METADATA_PROPERTY_TYPE_ID);
	}

	
	function set_metadata_property_type_id($metadata_property_type_id)
	{
		$this->set_default_property(self :: PROPERTY_METADATA_PROPERTY_TYPE_ID, $metadata_property_type_id);
	}

        function get_pub_value()
	{
		return $this->get_default_property(self :: PROPERTY_PUB_VALUE);
	}


	function set_pub_value($pub_value)
	{
		$this->set_default_property(self :: PROPERTY_PUB_VALUE, $pub_value);
	}


	static function get_table_name()
	{
//		return Utilities::get_classname_from_namespace(Utilities :: camelcase_to_underscores(self :: CLASS_NAME));

            return self :: TABLE_NAME;


	}
}

?>