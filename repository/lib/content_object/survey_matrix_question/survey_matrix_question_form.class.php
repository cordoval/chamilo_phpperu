<?php
/**
 * $Id: survey_matrix_question_form.class.php
 * @package repository.lib.content_object.survey_matrix_question
 */

require_once PATH :: get_repository_path() . '/question_types/matrix_question/matrix_question_form.class.php';

class SurveyMatrixQuestionForm extends MatrixQuestionForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/survey_matrix_question.js'));
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/survey_matrix_question.js'));
    }

    function create_content_object()
    {
        $object = new SurveyMatrixQuestion();
        return parent :: create_content_object($object);
    }

}
?>
