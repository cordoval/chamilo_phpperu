<?php
namespace repository\content_object\assessment_multiple_choice_question;

use common\libraries\Path;
use common\libraries\Translation;

use repository\ContentObjectDisplay;

/**
 * $Id: assessment_multiple_choice_question_display.class.php $
 * @package repository.lib.content_object.multiple_choice_question
 */

class AssessmentMultipleChoiceQuestionDisplay extends ContentObjectDisplay
{

    function get_full_html()
    {
        $html = parent :: get_full_html();
        return $html;
    }

    function get_description()
    {
        $html = array();

        $lo = $this->get_content_object();
        $options = $lo->get_options();
        $type = $lo->get_answer_type();

        switch ($type)
        {
            case AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_RADIO :
                $type = 'radio';
                break;
            case AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX :
                $type = 'checkbox';
                break;
        }

        $html[] = parent :: get_description();
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="checkbox"></th>';
        $html[] = '<th>' . Translation :: get('Option') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        foreach ($options as $index => $option)
        {
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td><input type="' . $type . '" name="option[]"/></td>';
            $html[] = '<td>' . $option->get_value() . '</td>';
            $html[] = '</tr>';
        }

        $html[] = '</tbody>';
        $html[] = '</table>';
        return implode("\n", $html);
    }
}
?>