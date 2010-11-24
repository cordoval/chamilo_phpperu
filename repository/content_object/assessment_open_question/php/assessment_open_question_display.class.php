<?php
namespace repository\content_object\assessment_open_question;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Utilities;
use repository\OpenQuestionDisplay;

/**
 * $Id: assessment_open_question_display.class.php $
 * @package repository.lib.content_object.assessment_open_question
 */

require_once Path :: get_repository_path() . '/question_types/open_question/open_question_display.class.php';
/**
 * This class can be used to display open questions
 */
class AssessmentOpenQuestionDisplay extends OpenQuestionDisplay
{
    function get_description()
    {
        $description = parent :: get_description();
        $object = $this->get_content_object();
        $type_id = $object->get_question_type();

        switch ($type_id)
        {
            case 1 :
                $type = Translation :: get('OpenQuestion');
                break;
            case 2 :
                $type = Translation :: get('OpenQuestionWithDocument');
                break;
            case 3 :
                $type = Translation :: get('DocumentQuestion');
                break;
            default :
                $type = Translation :: get('OpenQuestion');
                break;
        }

        $html = array();
        $html[] = $description;
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr><th>&nbsp;</th><th>&nbsp;</th></tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        $html[] = '<tr class="row_even">';
        $html[] = '<td>' . Translation :: get('Type', null, Utilities :: COMMON_LIBRARIES) . '</td>';
        $html[] = '<td>' . $type . '</td>';
        $html[] = '</tr>';
        $html[] = '<tr class="row_odd">';
        $html[] = '<td>' . Translation :: get('Feedback') . '</td>';
        $html[] = '<td>' . $object->get_feedback() . '</td>';
        $html[] = '</tr>';
        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode("\n", $html);
    }
}
?>