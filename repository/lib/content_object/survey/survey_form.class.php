<?php
/**
 * $Id: survey_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey
 */
require_once dirname(__FILE__) . '/survey.class.php';
//require_once dirname(__FILE__) . '/survey_context.class.php';
require_once dirname(__FILE__) . '/survey_context_template.class.php';

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
            $defaults[Survey :: PROPERTY_HEADER] = $object->get_header();
            $defaults[Survey :: PROPERTY_FOOTER] = $object->get_footer();
            $defaults[Survey :: PROPERTY_FINISH_TEXT] = $object->get_finish_text();
            $defaults[Survey :: PROPERTY_CONTEXT_TEMPLATE_ID] = $object->get_context_template_id();
        
        }
        
        parent :: setDefaults($defaults);
    }

    protected function build_creation_form()
    {
        
    	$html_editor_options = array();
	    $html_editor_options[FormValidatorHtmlEditorOptions :: OPTION_TOOLBAR] = 'RepositorySurveyQuestion';
    	
	    parent :: build_creation_form($html_editor_options);
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_html_editor(Survey :: PROPERTY_HEADER, Translation :: get('SurveyHeaderText'), false, $html_editor_options);
        $this->add_html_editor(Survey :: PROPERTY_FOOTER, Translation :: get('SurveyHeaderText'), false, $html_editor_options);
        $this->add_html_editor(Survey :: PROPERTY_FINISH_TEXT, Translation :: get('SurveyFinishText'), false, $html_editor_options);
        $this->addElement('checkbox', Survey :: PROPERTY_ANONYMOUS, Translation :: get('Anonymous'));
        $this->add_select(Survey :: PROPERTY_CONTEXT_TEMPLATE_ID, Translation :: get('SurveyContext'), $this->get_contexts(), true);
        $this->addElement('category');
    }

    // Inherited
    protected function build_editing_form()
    {
        $html_editor_options = array();
	    $html_editor_options[FormValidatorHtmlEditorOptions :: OPTION_TOOLBAR] = 'RepositorySurveyQuestion';
    	
    	parent :: build_editing_form($html_editor_options);
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_html_editor(Survey :: PROPERTY_HEADER, Translation :: get('SurveyHeaderText'), false, $html_editor_options);
        $this->add_html_editor(Survey :: PROPERTY_FOOTER, Translation :: get('SurveyHeaderText'), false, $html_editor_options);
        $this->add_html_editor(Survey :: PROPERTY_FINISH_TEXT, Translation :: get('SurveyFinishText'), false, $html_editor_options);
        $this->addElement('checkbox', Survey :: PROPERTY_ANONYMOUS, Translation :: get('Anonymous'));
        
        $survey = $this->get_content_object();
        $allowed = RepositoryDataManager :: content_object_deletion_allowed($survey, 'Context');
        $atributes = array();
        if (! $allowed)
        {
           
        	
        	$this->addElement('hidden', Survey :: PROPERTY_CONTEXT_TEMPLATE_ID, $survey->get_context_template_id());
        	$this->addElement('static', '', Translation :: get('SurveyContext'), $survey->get_context_template_name());
        	$this->add_warning_message('no_context_update', '', Translation :: get('CanNotUpdateSurveyContextBecauseSurveyIsPublished'));
            
        }else{
        	$this->add_select(Survey :: PROPERTY_CONTEXT_TEMPLATE_ID, Translation :: get('SurveyContext'), $this->get_contexts(), true);
        }
        
        $this->addElement('category');
    }

    // Inherited
    function create_content_object()
    {
        $object = new Survey();
        $values = $this->exportValues();
        
        $object->set_header($values[Survey :: PROPERTY_HEADER]);
        $object->set_footer($values[Survey :: PROPERTY_FOOTER]);
        
        $object->set_finish_text($values[Survey :: PROPERTY_FINISH_TEXT]);
        
        if (isset($values[Survey :: PROPERTY_ANONYMOUS]))
        {
            $object->set_anonymous($values[Survey :: PROPERTY_ANONYMOUS]);
        
        }
        else
        {
            $object->set_anonymous(0);
        
        }
        
        $object->set_context_template_id($values[Survey :: PROPERTY_CONTEXT_TEMPLATE_ID]);
        
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();

        $object->set_header($values[Survey :: PROPERTY_HEADER]);
        $object->set_footer($values[Survey :: PROPERTY_FOOTER]);
        $object->set_finish_text($values[Survey :: PROPERTY_FINISH_TEXT]);
        
        if (isset($values[Survey :: PROPERTY_ANONYMOUS]))
        {
            $object->set_anonymous($values[Survey :: PROPERTY_ANONYMOUS]);
        }
        else
        {
            $object->set_anonymous(0);
        
        }
        $object->set_context_template_id($values[Survey :: PROPERTY_CONTEXT_TEMPLATE_ID]);
        
        $this->set_content_object($object);
        return parent :: update_content_object();
    }

    public function get_contexts()
    {
        
    	$condition = new EqualityCondition(SurveyContextTemplate::PROPERTY_PARENT_ID, 0, SurveyContextTemplate :: get_table_name());
    	$templates = SurveyContextDataManager::get_instance()->retrieve_survey_context_templates($condition);
    	$selects = array();
        while ($template = $templates->next_result())
        {
           $selects[$template->get_id()] = $template->get_name();
        }
        return $selects;
    }
}
?>