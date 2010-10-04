<?php
/**
 * $Id: assessment_open_question.class.php $
 * @package repository.lib.content_object.assessment_open_question
 */

require_once PATH :: get_repository_path() . '/question_types/open_question/open_question.class.php';

/**
 * This class represents an open question
 */
class AssessmentOpenQuestion extends OpenQuestion
{
    const PROPERTY_QUESTION_TYPE = 'question_type';
	
	const TYPE_OPEN = 1;
	const TYPE_OPEN_WITH_DOCUMENT = 2;
    const TYPE_DOCUMENT = 3;

	const CLASS_NAME = __CLASS__;

	static function get_type_name() 
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
    
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