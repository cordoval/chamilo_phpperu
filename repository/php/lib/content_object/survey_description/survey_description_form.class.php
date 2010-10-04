<?php
/**
 * $Id: survey_description_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey_description
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/survey_description.class.php';
/**
 * A form to create/update a survey_description
 */
class SurveyDescriptionForm extends ContentObjectForm
{

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        parent :: set_values($defaults);
    }

    // Inherited
    function create_content_object()
    {
        $object = new SurveyDescription();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}

?>
