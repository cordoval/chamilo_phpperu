<?php
/**
 * $Id: open_question.class.php 
 * @package repository.question_types.open_question
 */
/**
 * This class represents an open question
 */
class OpenQuestion extends ContentObject
{
    function get_table()
    {
        return 'open_question';
    }
}
?>