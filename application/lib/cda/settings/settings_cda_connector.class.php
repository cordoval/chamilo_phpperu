<?php
/**
 * $Id: settings_cda_connector.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.cda.settings
 */
require_once Path :: get_application_path() . 'lib/cda/cda_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/cda/cda_language.class.php';

/**
 * Simple connector class to facilitate rendering settings forms by
 * preprocessing data from the datamanagers to a simple array format.
 * 
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class SettingsCdaConnector
{

    function get_source_languages()
    {
    	$languages = CdaDataManager :: get_instance()->retrieve_cda_languages();
    	$options = array();
    	
    	while($language = $languages->next_result())
    	{
    		$options[$language->get_id()] = $language->get_original_name();
    	}
    	
        return $options;
    }
}
?>
