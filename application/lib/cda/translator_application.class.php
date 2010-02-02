<?php 
/**
 * cda
 */
require_once dirname(__FILE__) . '/cda_rights.class.php';

/**
 * This class describes a TranslatorApplication data object
 * @author Hans De Bisschop
 * @author Hans De Bisschop
 */
class TranslatorApplication extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * TranslatorApplication properties
	 */
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_SOURCE_LANGUAGE_ID = 'source_language_id';
	const PROPERTY_DESTINATION_LANGUAGE_ID = 'destination_language_id';
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
		return parent :: get_default_property_names(array (self :: PROPERTY_USER_ID, self :: PROPERTY_SOURCE_LANGUAGE_ID, self :: PROPERTY_DESTINATION_LANGUAGE_ID, self :: PROPERTY_DATE, self :: PROPERTY_STATUS));
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
	
	function get_destination_language_id()
	{
		return $this->get_default_property(self :: PROPERTY_DESTINATION_LANGUAGE_ID);
	}
	
	function set_destination_language_id($destination_language_id)
	{
		$this->set_default_property(self :: PROPERTY_DESTINATION_LANGUAGE_ID, $destination_language_id);
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
    
    function get_destination_language()
    {
    	$cdm = CdaDataManager :: get_instance();
    	return $cdm->retrieve_cda_language($this->get_destination_language_id());
    }
    
    function activate()
    {
    	$source_setting = AdminDataManager :: get_instance()->retrieve_setting_from_variable_name('source_language', CdaManager :: APPLICATION_NAME);
    	$source_user_setting = UserDataManager :: get_instance()->retrieve_user_setting(Session :: get_user_id(), $source_setting->get_id());
    	
    	if(!$source_user_setting)
    	{
    		$source_user_setting = new UserSetting();
    		$source_user_setting->set_setting_id($source_setting->get_id());
    		$source_user_setting->set_value($this->get_source_language_id());
    		$source_user_setting->set_user_id($this->get_user_id());
    		$source_user_setting->create();
    	}
    	
   		$location = CdaRights :: get_location_id_by_identifier('cda_language', $this->get_destination_language_id());
    	$success = RightsUtilities :: set_user_right_location_value(CdaRights :: VIEW_RIGHT, $this->get_user_id(), $location, true);
    		
    	if (!$success)
    	{
    		return false;
    	}
    	
    	$this->set_status(self :: STATUS_ACCEPTED);
    	return $this->update();
    }
    
    function deactivate()
    {
    	$source_setting = AdminDataManager :: get_instance()->retrieve_setting_from_variable_name('source_language', CdaManager :: APPLICATION_NAME);
    	$source_user_setting = UserDataManager :: get_instance()->retrieve_user_setting(Session :: get_user_id(), $source_setting->get_id());
    	
    	if($source_user_setting)
    	{
    		$source_user_setting->delete();
    	}
    	
    	$location = CdaRights :: get_location_id_by_identifier('cda_language', $this->get_destination_language_id());
    	$success = RightsUtilities :: set_user_right_location_value(CdaRights :: VIEW_RIGHT, $this->get_user_id(), $location, false);
    	
    	if (!$success)
    	{
    		return false;
    	}
    	
    	$this->set_status(self :: STATUS_PENDING);
    	return $this->update();
    }
    
    function delete()
    {
    	if (!$this->deactivate())
    	{
    		return false;
    	}
    	
    	return parent :: delete();
    }
}

?>