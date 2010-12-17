<?php
namespace repository\content_object\assessment_select_question;

use common\libraries\Path;
use common\libraries\Translation;

use repository\ContentObjectDisplay;

/**
 * $Id: assessment_select_question_display.class.php $
 * @package repository.lib.content_object.select_question
 */
require_once dirname(__FILE__) . '/assessment_select_question_option.class.php';

class AssessmentSelectQuestionDisplay extends ContentObjectDisplay
{

    function get_description()
    {
        $html = array();

        $lo = $this->get_content_object();
        $options = $lo->get_options();
        $type = $lo->get_answer_type();

        $html[] = parent :: get_description();
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th>' . Translation :: get($type == 'radio' ? 'SelectCorrectAnswer' : 'SelectCorrectAnswers') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $select_options = array();
        foreach ($options as $option)
        {
            $select_options[] = '<option>' . $option->get_value() . '</option>';
        }

        $html[] = '<tr>';
        $html[] = '<td>';
        $html[] = '<select style="width: 200px;"' . ($type == 'checkbox' ? ' multiple="true"' : '') . '>';
        $html[] = implode("\n", $select_options);
        $html[] = '</select>';
        $html[] = '</td>';
        $html[] = '</tr>';

        $html[] = '</tbody>';
        $html[] = '</table>';
        return implode("\n", $html);
    }
}
?>