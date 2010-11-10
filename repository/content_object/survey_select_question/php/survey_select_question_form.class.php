<?php
namespace repository\content_object\survey_select_question;

use common\libraries\Path;
use common\libraries\ResourceManager;
use repository\SelectQuestionForm;

/**
 * $Id: survey_select_question_form.class.php $
 * @package repository.lib.content_object.survey_select_question
 */
require_once Path :: get_repository_path() . '/question_types/select_question/select_question_form.class.php';

class SurveySelectQuestionForm extends SelectQuestionForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'repository/content_object/survey_select_question/resources/javascript/survey_select_question.js'));
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'repository/content_object/survey_select_question/resources/javascript/survey_select_question.js'));
    }

    function create_content_object()
    {
        $object = new SurveySelectQuestion();
        return parent :: create_content_object($object);
    }
}
?>