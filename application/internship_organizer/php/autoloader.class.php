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
            'internship_organizer_manager' => 'lib/internship_organizer_manager/internship_organizer_manager.class.php',
            'internship_organizer_agreement_manager' => 'lib/agreement_manager/agreement_manager.class.php',
            'internship_organizer_appointment_manager' => 'lib/appointment_manager/appointment_manager.class.php',
            'internship_organizer_category_manager' => 'lib/category_manager/category_manager.class.php',
            'internship_organizer_evaluation_manager' => 'lib/evaluation_manager/evaluation_manager.class.php',
            'internship_organizer_organisation_manager' => 'lib/organisation_manager/organisation_manager.class.php',
            'internship_organizer_period_manager' => 'lib/period_manager/period_manager.class.php',
            'internship_organizer_region_manager' => 'lib/region_manager/region_manager.class.php',
            'default_internship_organizer_category_rel_location_table_column_model' => 'lib/tables/category_rel_location_table/default_category_rel_location_table_column_model.class.php',
            'default_internship_organizer_category_rel_location_table_cell_renderer' => 'lib/tables/category_rel_location_table/default_category_rel_location_table_cell_renderer.class.php',
            'default_internship_organizer_location_table_column_model' => 'lib/tables/location_table/default_location_table_column_model.class.php',
            'default_internship_organizer_location_table_cell_renderer' => 'lib/tables/location_table/default_location_table_cell_renderer.class.php',
            'internship_organizer_category_form' => 'lib/forms/category_form.class.php',
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