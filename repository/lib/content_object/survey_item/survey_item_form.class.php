<?php
/**
 * $Id: survey_item_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey_item
 */
require_once dirname(__FILE__) . '/survey_item.class.php';

class SurveyItemForm extends ContentObjectForm
{

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[SurveyItem :: PROPERTY_REFERENCE] = $valuearray[3];
        parent :: set_values($defaults);
    }

    function create_content_object()
    {
        $object = new SurveyItem();
        $object->set_reference($this->exportValue(SurveyItem :: PROPERTY_REFERENCE));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_reference($this->exportValue(SurveyItem :: PROPERTY_REFERENCE));
        return parent :: update_content_object();
    }

    function build_creation_form($default_content_object = null)
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('text', SurveyItem :: PROPERTY_REFERENCE, Translation :: get('Reference'));
        $this->addElement('category');
    }

    function build_editing_form($object)
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('text', SurveyItem :: PROPERTY_REFERENCE, Translation :: get('Reference'));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        $defaults[SurveyItem :: PROPERTY_REFERENCE] = $object->get_reference();
        parent :: setDefaults($defaults);
    }

}
?>
