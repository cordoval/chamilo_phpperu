<?php

namespace application\package;

use common\libraries\WebApplication;
/**
 * $Id: settings_package_connector.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.package.settings
 */
require_once WebApplication :: get_application_class_lib_path('package') . 'package_data_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_language.class.php';

/**
 * Simple connector class to facilitate rendering settings forms by
 * preprocessing data from the datamanagers to a simple array format.
 * 
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class SettingsPackageConnector
{

    function get_source_languages()
    {
    	$languages = PackageDataManager :: get_instance()->retrieve_package_languages();
    	$options = array();
    	
    	while($language = $languages->next_result())
    	{
    		$options[$language->get_id()] = $language->get_original_name();
    	}
    	
        return $options;
    }
}
?>