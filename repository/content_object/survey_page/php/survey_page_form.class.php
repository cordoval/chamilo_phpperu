<?php
namespace repository\content_object\survey_page;

use common\libraries\Translation;
use repository\ContentObjectForm;
use common\libraries\FormValidatorHtmlEditorOptions;

/**
 * $Id: survey_page_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey_page
 */
//require_once dirname(__FILE__) . '/survey_page.class.php';
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
        $html_editor_options = array();
        $html_editor_options[FormValidatorHtmlEditorOptions :: OPTION_TOOLBAR] = 'RepositorySurveyQuestion';
        
        //    	parent :: build_creation_form($html_editor_options);
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));
        //        $this->add_html_editor(SurveyPage :: PROPERTY_INTRODUCTION_TEXT, Translation :: get('SurveyPageHeaderText'), false, $html_editor_options);
        //        $this->add_html_editor(SurveyPage :: PROPERTY_FINISH_TEXT, Translation :: get('SurveyPageFooterText'), false, $html_editor_options);
        $this->add_html_editor(SurveyPage :: PROPERTY_INTRODUCTION_TEXT, Translation :: get('SurveyPageHeaderText'), false);
        $this->add_html_editor(SurveyPage :: PROPERTY_FINISH_TEXT, Translation :: get('SurveyPageFooterText'), false);
        
        $this->addElement('category');
    }

    // Inherited
    protected function build_editing_form()
    {
        
        $html_editor_options = array();
        $html_editor_options[FormValidatorHtmlEditorOptions :: OPTION_TOOLBAR] = 'RepositorySurveyQuestion';

    	//    	parent :: build_creation_form($html_editor_options);
    	
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));
        //        $this->add_html_editor(SurveyPage :: PROPERTY_INTRODUCTION_TEXT, Translation :: get('SurveyPageHeaderText'), false, $html_editor_options);
        //        $this->add_html_editor(SurveyPage :: PROPERTY_FINISH_TEXT, Translation :: get('SurveyPageFooterText'), false, $html_editor_options);
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