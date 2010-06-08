<?php
/**
 * $Id: import_survey_question_option.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a survey answer
 * @author Van Wayenbergh David
 */
abstract class ImportSurveyQuestionOption extends Import
{

    abstract function is_valid($array);

    abstract function convert_to_lcms($array);

    abstract static function get_all($array);
}
?>