<?php
/**
 * $Id: survey_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey
 */
require_once dirname(__FILE__) . '/survey.class.php';
require_once dirname(__FILE__) . '/survey_context.class.php';
/**
 * This class represents a form to create or update assessment
 */
class SurveyForm extends ContentObjectForm
{

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        if ($object != null)
        {
            $defaults[Survey :: PROPERTY_ANONYMOUS] = $object->get_anonymous();
            $defaults[Survey :: PROPERTY_INTRODUCTION_TEXT] = $object->get_introduction_text();
            $defaults[Survey :: PROPERTY_FINISH_TEXT] = $object->get_finish_text();
            $defaults[Survey :: PROPERTY_CONTEXT] = $object->get_context()->get_type();
        
        }
        
        parent :: setDefaults($defaults);
    }

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_html_editor(Survey :: PROPERTY_INTRODUCTION_TEXT, Translation :: get('SurveyHeaderText'), false);
        $this->add_html_editor(Survey :: PROPERTY_FINISH_TEXT, Translation :: get('SurveyFooterText'), false);
        $this->addElement('checkbox', Survey :: PROPERTY_ANONYMOUS, Translation :: get('Anonymous'));
        $this->add_select(Survey :: PROPERTY_CONTEXT, Translation :: get('SurveyContext'), $this->get_contexts(), true);
        $this->addElement('category');
    }

    // Inherited
    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_html_editor(Survey :: PROPERTY_INTRODUCTION_TEXT, Translation :: get('SurveyHeaderText'), false);
        $this->add_html_editor(Survey :: PROPERTY_FINISH_TEXT, Translation :: get('SurveyFooterText'), false);
        $this->addElement('checkbox', Survey :: PROPERTY_ANONYMOUS, Translation :: get('Anonymous'));
        
        $rdm = RepositoryDataManager :: get_instance();
        $survey = $this->get_content_object();
        $allowed = $rdm->content_object_deletion_allowed($survey, 'Context');
        $atributes = array();
        if (! $allowed)
        {
           
        	
        	$this->addElement('hidden', Survey :: PROPERTY_CONTEXT, $survey->get_context()->get_type());
        	$this->addElement('static', '', Translation :: get('SurveyContext'), $survey->get_context()->get_display_name());
        	$this->add_warning_message('no_context_update', '', Translation :: get('CanNotUpdateSurveyContextBecauseSurveyIsPublished'));
            
        }else{
        	$this->add_select(Survey :: PROPERTY_CONTEXT, Translation :: get('SurveyContext'), $this->get_contexts(), true);
        }
        
        $this->addElement('category');
    }

    // Inherited
    function create_content_object()
    {
        $object = new Survey();
        $values = $this->exportValues();
        
        $object->set_finish_text($values[Survey :: PROPERTY_FINISH_TEXT]);
        $object->set_introduction_text($values[Survey :: PROPERTY_INTRODUCTION_TEXT]);
        
        if (isset($values[Survey :: PROPERTY_ANONYMOUS]))
        {
            $object->set_anonymous($values[Survey :: PROPERTY_ANONYMOUS]);
        
        }
        else
        {
            $object->set_anonymous(0);
        
        }
        
        $object->set_context($values[Survey :: PROPERTY_CONTEXT]);
        
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
            
        $object->set_finish_text($values[Survey :: PROPERTY_FINISH_TEXT]);
        $object->set_introduction_text($values[Survey :: PROPERTY_INTRODUCTION_TEXT]);
        
        if (isset($values[Survey :: PROPERTY_ANONYMOUS]))
        {
            $object->set_anonymous($values[Survey :: PROPERTY_ANONYMOUS]);
        }
        else
        {
            $object->set_anonymous(0);
        
        }
        $object->set_context($values[Survey :: PROPERTY_CONTEXT]);
        
        $this->set_content_object($object);
        return parent :: update_content_object();
    }

    public function get_contexts()
    {
        $contexts = SurveyContext :: get_registered_contexts();
        $selects = array();
        foreach ($contexts as $context)
        {
           $selects[$context->get_type()] = $context->get_display_name();
        }
        return $selects;
    }
}
?>
