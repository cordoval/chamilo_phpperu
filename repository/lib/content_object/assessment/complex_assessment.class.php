<?php
/**
 * $Id: complex_assessment.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.assessment
 *
 */
/**
 * This class represents a complex assessment (used to create complex learning objects)
 */
class ComplexAssessment extends ComplexContentObjectItem
{

    function get_allowed_types()
    {
        return array('open_question', 'hotspot_question', 'fill_in_blanks_question', 'multiple_choice_question', 'matching_question', 'select_question', 'matrix_question');
    }
}
?>