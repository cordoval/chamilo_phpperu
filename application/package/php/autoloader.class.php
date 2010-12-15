<?php

namespace application\package;

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
                'package_data_manager' => 'package_data_manager.class.php', 'package_data_manager_interface' => 'package_data_manager_interface.class.php', 'package_rights' => 'package_rights.class.php', 
                'historic_variable_translation' => 'historic_variable_translation.class.php', 'variable_translation' => 'variable_translation.class.php', 'variable' => 'variable.class.php', 
                'translator_application' => 'translator_application.class.php', 'translation_exporter' => 'package_manager/component/translation_exporter/translation_exporter.class.php', 
                'translation_importer' => 'package_manager/component/translation_importer/translation_importer.class.php', 'language_pack' => 'language_pack.class.php', 
                'package_manager' => 'package_manager/package_manager.class.php', 'variable_form' => 'forms/variable_form.class.php', 
                'exporter_wizard_page' => 'package_manager/component/translation_exporter/pages/exporter_wizard_page.class.php', 'package' => 'package.class.php');
        
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('package') . $url;
            return true;
        }
        
        return false;
    }
}

?>