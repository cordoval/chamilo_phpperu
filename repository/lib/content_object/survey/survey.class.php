<?php
/**
 * $Id: survey.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey
 */
/**
 * This class represents an assessment
 */
class Survey extends ContentObject
{
    const PROPERTY_TIMES_TAKEN = 'times_taken';
    const PROPERTY_AVERAGE_SCORE = 'average_score';
    const PROPERTY_MAXIMUM_SCORE = 'maximum_score';
    const PROPERTY_MAXIMUM_ATTEMPTS = 'max_attempts';
    const PROPERTY_FINISH_TEXT = 'finish_text';
    const PROPERTY_INTRODUCTION_TEXT = 'intro_text';
    const PROPERTY_ANONYMOUS = 'anonymous';
    const PROPERTY_QUESTIONS_PER_PAGE = 'questions_per_page';

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_MAXIMUM_ATTEMPTS, self :: PROPERTY_QUESTIONS_PER_PAGE, self :: PROPERTY_INTRODUCTION_TEXT, self :: PROPERTY_FINISH_TEXT, self :: PROPERTY_ANONYMOUS);
    }
    
    const TYPE_SURVEY = 4;

    function get_assessment_type()
    {
        return self :: TYPE_SURVEY;
    }

    function get_introduction_text()
    {
        return $this->get_additional_property(self :: PROPERTY_INTRODUCTION_TEXT);
    }

    function set_introduction_text($text)
    {
        $this->set_additional_property(self :: PROPERTY_INTRODUCTION_TEXT, $text);
    }

    function get_maximum_attempts()
    {
        return $this->get_additional_property(self :: PROPERTY_MAXIMUM_ATTEMPTS);
    }

    function set_maximum_attempts($value)
    {
        $this->set_additional_property(self :: PROPERTY_MAXIMUM_ATTEMPTS, $value);
    }

    function get_finish_text()
    {
        return $this->get_additional_property(self :: PROPERTY_FINISH_TEXT);
    }

    function set_finish_text($value)
    {
        $this->set_additional_property(self :: PROPERTY_FINISH_TEXT, $value);
    }

    function get_anonymous()
    {
        return $this->get_additional_property(self :: PROPERTY_ANONYMOUS);
    }

    function set_anonymous($value)
    {
        return $this->set_additional_property(self :: PROPERTY_ANONYMOUS, $value);
    }

    function get_allowed_types()
    {
        $allowed_types = array();
        $allowed_types[] = 'rating_question';
        $allowed_types[] = 'open_question';
        $allowed_types[] = 'hotspot_question';
        $allowed_types[] = 'fill_in_blanks_question';
        $allowed_types[] = 'multiple_choice_question';
        $allowed_types[] = 'matching_question';
        $allowed_types[] = 'select_question';
        $allowed_types[] = 'matrix_question';
        $allowed_types[] = 'match_question';
        $allowed_types[] = 'ordering_question';
        //$allowed_types[] = '';
        return $allowed_types;
    }

    function get_times_taken()
    {
        return WeblcmsDataManager :: get_instance()->get_num_user_assessments($this);
    }

    function get_table()
    {
        return 'survey';
    }

    function get_average_score()
    {
        return WeblcmsDataManager :: get_instance()->get_average_score($this);
    }

    function get_maximum_score()
    {
        return WeblcmsDataManager :: get_instance()->get_maximum_score($this);
    }

    function get_questions_per_page()
    {
        return $this->get_additional_property(self :: PROPERTY_QUESTIONS_PER_PAGE);
    }

    function set_questions_per_page($value)
    {
        $this->set_additional_property(self :: PROPERTY_QUESTIONS_PER_PAGE, $value);
    }
}
?>