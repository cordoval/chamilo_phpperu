<?php 
/**
 * cda
 */

/**
 * This class describes a TranslatorApplication data object
 * @author Hans De Bisschop
 * @author 
 */
class TranslatorApplication extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * TranslatorApplication properties
	 */
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_SOURCE_LANGUAGE_ID = 'source_language_id';
	const PROPERTY_DESTINATION_LANGUAGE_IDS = 'destination_language_ids';
	const PROPERTY_DATE = 'date';
	const PROPERTY_STATUS = 'status';

	const STATUS_PENDING = 1;
	const STATUS_ACCEPTED = 2;
	
	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return parent :: get_default_property_names(array (self :: PROPERTY_USER_ID, self :: PROPERTY_SOURCE_LANGUAGE_ID, self :: PROPERTY_DESTINATION_LANGUAGE_IDS, self :: PROPERTY_DATE, self :: PROPERTY_STATUS));
	}

	function get_data_manager()
	{
		return CdaDataManager :: get_instance();
	}
	
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	
	function get_source_language_id()
	{
		return $this->get_default_property(self :: PROPERTY_SOURCE_LANGUAGE_ID);
	}
	
	function set_source_language_id($source_language)
	{
		$this->set_default_property(self :: PROPERTY_SOURCE_LANGUAGE_ID, $source_language);
	}
	
	function get_destination_language_ids()
	{
		return $this->get_default_property(self :: PROPERTY_DESTINATION_LANGUAGE_IDS);
	}
	
	function set_destination_language_ids($destination_language_ids)
	{
		$this->set_default_property(self :: PROPERTY_DESTINATION_LANGUAGE_IDS, $destination_language_ids);
	}
	
	function get_date()
	{
		return $this->get_default_property(self :: PROPERTY_DATE);
	}
	
	function set_date($date)
	{
		$this->set_default_property(self :: PROPERTY_DATE, $date);
	}
	
	function get_status()
	{
		return $this->get_default_property(self :: PROPERTY_STATUS);
	}
	
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}
	
	function get_status_icon()
	{
		switch($this->get_status())
		{
			case self :: STATUS_PENDING:
				$image = 'status_pending';
				break;
			case self :: STATUS_ACCEPTED:
				$image = 'status_accepted';
				break;
		}
		
		return Theme :: get_image($image);
	}
	
	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
    function get_user()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($this->get_user_id());
    }
    
    function get_source_language()
    {
    	$cdm = CdaDataManager :: get_instance();
    	return $cdm->retrieve_cda_language($this->get_source_language_id());
    }
    
    function get_destination_languages()
    {
    	$cdm = CdaDataManager :: get_instance();
    	$condition = new InCondition(CdaLanguage :: PROPERTY_ID, unserialize($this->get_destination_language_ids()));
    	return $cdm->retrieve_cda_languages($condition, null, null, array(new ObjectTableOrder(CdaLanguage :: PROPERTY_ENGLISH_NAME)));
    }
}

?>