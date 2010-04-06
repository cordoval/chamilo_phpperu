<?php
/**
 * $Id: survey_multiple_choice_question_form.class.php $
 * @package repository.lib.content_object.survey_multiple_choice_question
 */
require_once PATH :: get_repository_path() . '/question_types/multiple_choice_question/multiple_choice_question_form.class.php';

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

	function create_content_object()
    {
        $object = new SurveyMultipleChoiceQuestion();
        return parent :: create_content_object($object);
    }
}
?>