<?php
/**
 * $Id: survey_multiple_choice_question_form.class.php $
 * @package repository.lib.content_object.survey_multiple_choice_question
 */
require_once PATH :: get_repository_path() . '/question_types/multiple_choice_question/multiple_choice_question_form.class.php';
require_once dirname(__FILE__) . '/survey_multiple_choice_question_option.class.php';

class SurveyMultipleChoiceQuestionForm extends MultipleChoiceQuestionForm
{
    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/survey_multiple_choice_question.js'));
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/survey_multiple_choice_question.js'));
    }

    function add_options_to_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();

        $options = array();
        foreach ($values[SurveyMultipleChoiceQuestionOption :: PROPERTY_VALUE] as $option_id => $value)
        {
            $options[] = new SurveyMultipleChoiceQuestionOption($value);
        }

        $object->set_answer_type($_SESSION['mc_answer_type']);
        $object->set_options($options);
    }

	function create_content_object()
    {
        $object = new SurveyMultipleChoiceQuestion();
        return parent :: create_content_object($object);
    }
}
?>