<?php
/**
 * $Id: survey_page_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey_page
 */
require_once dirname(__FILE__) . '/survey_page.class.php';
/**
 * This class represents a form to create or update assessment
 */
class SurveyPageForm extends ContentObjectForm
{

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        if ($object != null)
        {
            
            $defaults[SurveyPage :: PROPERTY_INTRODUCTION_TEXT] = $object->get_introduction_text();
            $defaults[SurveyPage :: PROPERTY_FINISH_TEXT] = $object->get_finish_text();

        }
        
        parent :: setDefaults($defaults);
    }

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_html_editor(SurveyPage :: PROPERTY_INTRODUCTION_TEXT, Translation :: get('SurveyPageHeaderText'), false);
        $this->add_html_editor(SurveyPage :: PROPERTY_FINISH_TEXT, Translation :: get('SurveyPageFooterText'), false);
        $this->addElement('category');
    }

    // Inherited
    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_html_editor(SurveyPage :: PROPERTY_INTRODUCTION_TEXT, Translation :: get('SurveyPageHeaderText'), false);
        $this->add_html_editor(SurveyPage :: PROPERTY_FINISH_TEXT, Translation :: get('SurveyPageFooterText'), false);
        $this->addElement('category');
    }

    // Inherited
    function create_content_object()
    {
        $object = new SurveyPage();
        $values = $this->exportValues();
        
        $object->set_finish_text($values[SurveyPage :: PROPERTY_FINISH_TEXT]);
        $object->set_introduction_text($values[SurveyPage :: PROPERTY_INTRODUCTION_TEXT]);
    
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        
        $object->set_finish_text($values[SurveyPage :: PROPERTY_FINISH_TEXT]);
        $object->set_introduction_text($values[SurveyPage :: PROPERTY_INTRODUCTION_TEXT]);

        
        $this->set_content_object($object);
        return parent :: update_content_object();
    }

}
?>