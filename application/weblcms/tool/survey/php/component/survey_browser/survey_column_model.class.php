<?php
namespace application\weblcms\tool\survey;
use application\weblcms\ObjectPublicationTableColumnModel;
/**
 * $Id: survey_column_model.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_browser
 */
/**
 * This class is a cell renderer for a publication candidate table
 */
class SurveyColumnModel extends ObjectPublicationTableColumnModel
{

    function __construct()
    {
        parent :: __construct($this->get_columns());
    }

    function get_columns()
    {
        $columns = parent :: get_basic_columns();
        $columns[] = parent :: get_action_column();
        return $columns;
    }
}
?>