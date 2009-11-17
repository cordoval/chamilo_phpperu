<?php
/**
 * $Id: open_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.open_question
 */
/**
 * This class represents an open question
 */
class OpenQuestion extends ContentObject
{
    const PROPERTY_QUESTION_TYPE = 'question_type';
    
    const TYPE_OPEN = 1;
    const TYPE_OPEN_WITH_DOCUMENT = 2;
    const TYPE_DOCUMENT = 3;

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_QUESTION_TYPE);
    }

    function get_question_type()
    {
        return $this->get_additional_property(self :: PROPERTY_QUESTION_TYPE);
    }

    function set_question_type($question_type)
    {
        $this->set_additional_property(self :: PROPERTY_QUESTION_TYPE, $question_type);
    }

    function get_table()
    {
        return 'open_question';
    }

    function get_types()
    {
        $types = array();
        $types[self :: TYPE_OPEN] = Translation :: get('OpenQuestion');
        $types[self :: TYPE_OPEN_WITH_DOCUMENT] = Translation :: get('OpenQuestionWithDocument');
        $types[self :: TYPE_DOCUMENT] = Translation :: get('DocumentQuestion');
        return $types;
    }
}
?>