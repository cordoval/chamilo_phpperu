<?php
namespace application\phrases;

use common\libraries\Utilities;
use common\libraries\WebApplication;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class Autoloader
{

    static function load($classname)
    {
        $list = array('phrases_manager' => 'phrases_manager/phrases_manager',
                'results_export' => 'phrases_manager/component/results_export_form/export',
                'phrases_rights' => 'phrases_rights',
                'phrases_publication' => 'phrases_publication',
                'phrases_publication_user' => 'phrases_publication_user',
                'phrases_publication_group' => 'phrases_publication_group',
                'phrases_publication_category_menu' => 'phrases_publication_category_menu',
                'phrases_data_manager' => 'phrases_data_manager',
                'phrases_data_manager_interface' => 'phrases_data_manager_interface',
                'phrases_publication_form' => 'forms/phrases_publication_form',
                'default_phrases_publication_table_cell_renderer' => 'tables/phrases_publication_table/default_phrases_publication_table_cell_renderer',
                'default_phrases_publication_table_column_model' => 'tables/phrases_publication_table/default_phrases_publication_table_column_model',
                'phrases_publisher' => 'publisher/phrases_publisher',
                'phrases_publication_category_manager' => 'category_manager/phrases_publication_category_manager',
                'phrases_publication_category' => 'category_manager/phrases_publication_category',
                'phrases_publication_browser_table_cell_renderer' => 'phrases_manager/component/phrases_publication_browser/phrases_publication_browser_table_cell_renderer',
                'phrases_publication_browser_table_column_model' => 'phrases_manager/component/phrases_publication_browser/phrases_publication_browser_table_column_model',
                'phrases_publication_browser_table_data_provider' => 'phrases_manager/component/phrases_publication_browser/phrases_publication_browser_table_data_provider',
                'phrases_publication_browser_table' => 'phrases_manager/component/phrases_publication_browser/phrases_publication_browser_table',
                'results_export_form' => 'phrases_manager/component/results_export_form/results_export_form',
                'export' => 'phrases_manager/component/results_export_form/export',
                'phrases_adaptive_assessment_attempt_tracker' => 'phrases_adaptive_assessment_attempt_tracker',
                'phrases_adaptive_assessment_item_attempt_tracker' => '../trackers/phrases_adaptive_assessment_item_attempt_tracker',
                'phrases_adaptive_assessment_question_attempts_tracker' => '../trackers/phrases_adaptive_assessment_question_attempts_tracker');

        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('phrases') . $url . '.class.php';
            return true;
        }

        return false;
    }

}
?>