<?php

require_once Path::get_repository_path () . 'lib/content_object/survey/survey_context_template.class.php';


class SubscribeContextTemplateForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'SurveyContextTemplateSubscribed';
    const RESULT_ERROR = 'SurveyContextTemplateSubscribtionFailed';
    
    private $survey;
    private $user;

    function SubscribeContextTemplateForm($form_type, $survey, $action, $user)
    {
        parent :: __construct('subscribe_context_template', 'post', $action);
        
        $this->survey = $survey;
        $this->user = $user;
        $this->form_type = $form_type;
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    function build_basic_form()
    {
        
        $this->addElement('select', Survey :: PROPERTY_CONTEXT_TEMPLATE_ID, Translation :: get('SurveyContextTemplate'), $this->get_registered_context_templates());
        $this->addRule(Survey :: PROPERTY_CONTEXT_TEMPLATE_ID, Translation :: get('ThisFieldIsRequired'), 'required');
    
    }

    function build_editing_form()
    {
        
        $this->build_basic_form();
        
        $this->addElement('hidden', SurveyContextTemplate :: PROPERTY_ID);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function change_context_template()
    {
        $survey = $this->survey;
        $values = $this->exportValues();
        
        //        if ($value)
        //        {
        //            Event :: trigger('update', 'template', array('target_template_id' => $template->get_id(), 'action_user_id' => $this->user->get_id()));
        //        }
        

        return $value;
    }

    function subscribe_context_template()
    {
        
        $values = $this->exportValues();
        
        $this->survey->set_context_template_id( $values[Survey :: PROPERTY_CONTEXT_TEMPLATE_ID]);
        
        return $this->survey->update();
    
        
        //        if ($value)
        //        {
        //            Event :: trigger('create', 'template', array('target_template_id' => $template->get_id(), 'action_user_id' => $this->user->get_id()));
        //        }
        

        return $value;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $survey = $this->survey;
        $defaults[Survey :: PROPERTY_CONTEXT_TEMPLATE_ID] = $survey->get_context_template_id();
        parent :: setDefaults($defaults);
    }

    function get_template()
    {
        return $this->template;
    }

    function get_registered_context_templates()
    {
        $context_templates = array();
        $condition = new EqualityCondition(SurveyContextTemplate :: PROPERTY_PARENT_ID, 1);
        $survey_context_templates = SurveyContextDataManager :: get_instance()->retrieve_survey_context_templates($condition);
        while ($survey_context_template = $survey_context_templates->next_result())
        {
            
            $context_templates[$survey_context_template->get_id()] = $survey_context_template->get_name();
        }
        
        return $context_templates;
    }
}
?>