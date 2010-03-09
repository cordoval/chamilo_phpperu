<?php
/**
 * $Id: assessment.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.assessment
 *
 */
/**
 * This class represents an assessment
 */
class Assessment extends ContentObject
{
    const PROPERTY_ASSESSMENT_TYPE = 'assessment_type';
    
    const TYPE_EXERCISE = 1;
    const TYPE_ASSIGNMENT = 2;
    
    const PROPERTY_TIMES_TAKEN = 'times_taken';
    const PROPERTY_AVERAGE_SCORE = 'average_score';
    const PROPERTY_MAXIMUM_SCORE = 'maximum_score';
    const PROPERTY_MAXIMUM_ATTEMPTS = 'max_attempts';
    const PROPERTY_QUESTIONS_PER_PAGE = 'questions_per_page';
    const PROPERTY_MAXIMUM_TIME = 'max_time';
    const PROPERTY_RANDOM_QUESTIONS = 'random_questions';

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_ASSESSMENT_TYPE, self :: PROPERTY_MAXIMUM_ATTEMPTS, self :: PROPERTY_QUESTIONS_PER_PAGE, self :: PROPERTY_MAXIMUM_TIME, self :: PROPERTY_RANDOM_QUESTIONS);
    }

    function get_assessment_type()
    {
        return $this->get_additional_property(self :: PROPERTY_ASSESSMENT_TYPE);
    }

    function set_assessment_type($type)
    {
        $this->set_additional_property(self :: PROPERTY_ASSESSMENT_TYPE, $type);
    }

    function get_maximum_attempts()
    {
        return $this->get_additional_property(self :: PROPERTY_MAXIMUM_ATTEMPTS);
    }

    function set_maximum_attempts($value)
    {
        $this->set_additional_property(self :: PROPERTY_MAXIMUM_ATTEMPTS, $value);
    }

    function get_maximum_time()
    {
        return $this->get_additional_property(self :: PROPERTY_MAXIMUM_TIME);
    }

    function set_maximum_time($value)
    {
        $this->set_additional_property(self :: PROPERTY_MAXIMUM_TIME, $value);
    }

    function get_questions_per_page()
    {
        return $this->get_additional_property(self :: PROPERTY_QUESTIONS_PER_PAGE);
    }

    function set_questions_per_page($value)
    {
        $this->set_additional_property(self :: PROPERTY_QUESTIONS_PER_PAGE, $value);
    }

    function get_random_questions()
    {
        return $this->get_additional_property(self :: PROPERTY_RANDOM_QUESTIONS);
    }

    function set_random_questions($random_questions)
    {
        $this->set_additional_property(self :: PROPERTY_RANDOM_QUESTIONS, $random_questions);
    }

	function get_allowed_types()
    {
        $allowed_types = array();
        $allowed_types[] = 'assessment_rating_question';
        $allowed_types[] = 'assessment_open_question';
        $allowed_types[] = 'hotspot_question';
        $allowed_types[] = 'assessment_fill_in_blanks_question';
        $allowed_types[] = 'assessment_multiple_choice_question';
        $allowed_types[] = 'assessment_matching_question';
        $allowed_types[] = 'assessment_select_question';
        $allowed_types[] = 'assessment_matrix_question';
        $allowed_types[] = 'match_question';
        $allowed_types[] = 'ordering_question';
        //$allowed_types[] = '';
        return $allowed_types;
    }

    function get_table()
    {
        return 'assessment';
    }

    function get_times_taken()
    {
        return WeblcmsDataManager :: get_instance()->get_num_user_assessments($this);
    }

    function get_average_score()
    {
        return WeblcmsDataManager :: get_instance()->get_average_score($this);
    }

    function get_maximum_score()
    {
        return WeblcmsDataManager :: get_instance()->get_maximum_score($this);
    }

    function get_types()
    {
        $types = array();
        $types[self :: TYPE_EXERCISE] = Translation :: get('Exercise');
        $types[self :: TYPE_ASSIGNMENT] = Translation :: get('Assignment');
        asort($types);
        return $types;
    }
}
?>