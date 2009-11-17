<?php
/**
 * $Id: open_question_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.open_question
 */
/**
 * This class can be used to display open questions
 */
class OpenQuestionDisplay extends ContentObjectDisplay
{

    function get_full_html()
    {
        return parent :: get_full_html();
    }

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