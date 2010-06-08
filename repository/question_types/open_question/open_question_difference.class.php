<?php
/**
 * $Id: open_question_difference.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.open_question
 */
/**
 * This class can be used to get the difference between open question
 */
class OpenQuestionDifference extends ContentObjectDifference
{

    function get_difference()
    {
        $object = $this->get_object();
        $version = $this->get_version();
        
        $object_string = $object->get_question_type();
        $version_string = $version->get_question_type();
        
        $td = new Difference_Engine($object_string, $version_string);
        
        return array_merge($td->getDiff(), parent :: get_difference());
    }
}
?>