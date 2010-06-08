<?php
/**
 * $Id: survey_matching_question_form.class.php $
 * @package repository.lib.content_object.survey_matching_question
 */
require_once PATH :: get_repository_path() . '/question_types/matching_question/matching_question_form.class.php';

class SurveyMatchingQuestionForm extends MatchingQuestionForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/survey_matching_question.js'));
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/survey_matching_question.js'));
    }

    /**
     * Adds the answer to the current learning object.
     * This function adds the list of possible options and matches and the
     * relation between the options and the matches to the question.
     */
    function add_answer()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        $options = array();
        $matches = array();

        //Get an array with a mapping from the match-id to its index in the $values['match'] array
        $matches_indexes = array_flip(array_keys($values['match']));
        foreach ($values[MatchingQuestionOption::PROPERTY_VALUE] as $option_id => $value)
        {
            //Create the option with it corresponding match
            $options[] = new SurveyMatchingQuestionOption($value);
        }

        foreach ($values['match'] as $match)
        {
            $matches[] = $match;
        }
        $object->set_options($options);
        $object->set_matches($matches);
    }

    /**
     * Adds the options and matches to the form
     */
    function create_content_object()
    {
        $object = new SurveyMatchingQuestion();
        return parent :: create_content_object($object);
    }
}
?>