<?php
namespace repository\content_object\survey_open_question;

use common\libraries\Path;
use repository\ContentObjectForm;

/**
 * @package repository.content_object.survey_open_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents a form to create or update open questions
 */
class SurveyOpenQuestionForm extends ContentObjectForm
{

    function setDefaults($defaults = array ())
    {
        parent :: setDefaults($defaults);
    }

    function build_creation_form()
    {
        parent :: build_creation_form();
    }

    // Inherited
    function build_editing_form()
    {
        parent :: build_editing_form();
    }

    // Inherited
    function create_content_object()
    {
        $object = new SurveyOpenQuestion();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $this->set_content_object($object);

        return parent :: update_content_object();
    }

}