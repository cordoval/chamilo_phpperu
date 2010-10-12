<?php
/**
 * $Id: assessment_open_question_display.class.php $
 * @package repository.lib.content_object.assessment_open_question
 */

require_once PATH :: get_repository_path() . '/question_types/open_question/open_question_display.class.php';
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
        
        return '<b>' . Translation :: get('Type') . ':</b> ' . $type . $description;
    }
}
?>