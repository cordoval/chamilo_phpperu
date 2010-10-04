<?php
/**
 * @package admin.lib
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

class AdminDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return AdminDataManagerInterface The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_admin_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'AdminDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    function get_languages($use_folder_name_as_key = true)
    {
        $options = array();

        $languages = self :: get_instance()->retrieve_languages();
        while ($language = $languages->next_result())
        {
    		if(self :: is_language_active($language->get_english_name()))
    		{
    		    if ($use_folder_name_as_key)
    		    {
    		        $key = $language->get_folder();
    		    }
    		    else
    		    {
    		        $key = $language->get_id();
    		    }
        		$options[$key] = $language->get_original_name();
    		}
        }

        return $options;
    }

    static function is_language_active($language_name)
    {
    	$conditions = array();
    	$conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_LANGUAGE);
    	$conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, $language_name);
    	$condition = new AndCondition($conditions);

    	$registration = self :: get_instance()->retrieve_registrations($condition)->next_result();

    	if(!$registration)
    	{
    		return false;
    	}

    	return ($registration->get_status() == Registration :: STATUS_ACTIVE);
    }

    static function is_registered($name, $type = Registration :: TYPE_APPLICATION)
    {
    	$conditions = array();
    	$conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, $name);
    	$conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, $type);
    	$condition = new AndCondition($conditions);

    	return (self :: get_instance()->count_registrations($condition) > 0);
    }
    
	static function get_registration($name, $type = Registration :: TYPE_APPLICATION)
    {
    	$conditions = array();
    	$conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, $name);
    	$conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, $type);
    	$condition = new AndCondition($conditions);

    	return self :: get_instance()->retrieve_registrations($condition)->next_result();
    }

}
?>