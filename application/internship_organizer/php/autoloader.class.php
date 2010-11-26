<?php

namespace application\internship_organizer;

use common\libraries\Utilities;
use common\libraries\WebApplication;

class Autoloader
{

    static function load($classname)
    {

        $list = array(
            'excel_region_creator' => 'lib/import/excel/excel_region_creator.class.php',
            'internship_organizer_import' => 'lib/import/internship_organizer_import.class.php',
        );



        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/' . $url;
            return true;
        }

        return false;
    }

}

?>