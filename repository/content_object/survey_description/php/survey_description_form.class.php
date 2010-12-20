<?php
namespace repository\content_object\survey_description;

use repository\ContentObjectForm;

/**
 * @package repository.content_object.survey_description
 * @author Eduard Vossen
 * @author Magali Gillard
 */
require_once dirname(__FILE__) . '/survey_description.class.php';
/**
 * A form to create/update a survey_description
 */
class SurveyDescriptionForm extends ContentObjectForm
{

    // Inherited
    function create_content_object()
    {
        $object = new SurveyDescription();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}

?>
