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
	const PROPERTY_SOURCE_LANGUAGE = 'source_language_id';
	const PROPERTY_DESTINATION_LANGUAGES = 'destination_languages';
	const PROPERTY_DATE = 'date';
	const PROPERTY_STATUS = 'status';

	const STATUS_PENDING = 1;
	const STATUS_ACCEPTED = 2;
	const STATUS_REJECTED = 3;
	
	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_USER_ID, self :: PROPERTY_SOURCE_LANGUAGE, self :: PROPERTY_DESTINATION_LANGUAGES, self :: PROPERTY_DATE, self :: PROPERTY_STATUS);
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
	
	function get_source_language()
	{
		return $this->get_default_property(self :: PROPERTY_SOURCE_LANGUAGE);
	}
	
	function set_source_language($source_language)
	{
		$this->set_default_property(self :: PROPERTY_SOURCE_LANGUAGE, $source_language);
	}
	
	function get_destination_languages()
	{
		return $this->get_default_property(self :: PROPERTY_DESTINATION_LANGUAGES);
	}
	
	function set_destination_languages($destination_languages)
	{
		$this->set_default_property(self :: PROPERTY_DESTINATION_LANGUAGES, $destination_languages);
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
			case self :: STATUS_REJECTED:
				$image = 'status_rejected';
				break;
		}
		
		return Theme :: get_image($image);
	}
	
	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>