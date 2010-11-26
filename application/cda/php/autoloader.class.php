<?php

namespace application\cda;

use common\libraries\Utilities;
use common\libraries\WebApplication;
/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */

class Autoloader
{
	static function load($classname)
	{
            $list = array(

		'cda_data_manager' => 'cda_data_manager.class.php',
		'cda_data_manager_interface' => 'cda_data_manager_interface.class.php',
		'cda_rights' => 'cda_rights.class.php',
		'historic_variable_translation' => 'historic_variable_translation.class.php',
		'variable_translation' => 'variable_translation.class.php',
		'variable' => 'variable.class.php',
		'translator_application' => 'translator_application.class.php',
        'translation_exporter' => 'cda_manager/component/translation_exporter/translation_exporter.class.php',
		'language_pack' => 'language_pack.class.php',
		'cda_language' => 'cda_language.class.php',
		'cda_manager' => 'cda_manager/cda_manager.class.php',
		'variable_form' => 'forms/variable_form.class.php',
                'exporter_wizard_page' => 'cda_manager/component/translation_exporter/pages/exporter_wizard_page.class.php',
            );
		     
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('cda') . $url;
            return true;
        }
        
        return false;
	}
}

?>