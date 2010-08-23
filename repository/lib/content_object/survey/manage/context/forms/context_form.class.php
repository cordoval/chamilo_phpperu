<?php

class SurveyContextForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ContextUpdated';
    const RESULT_ERROR = 'ContextUpdateFailed';
          
    private $survey_context;
    private $user;
    private $form_type;
    private $manager;

    /**
     * Creates a new LanguageForm
     */
    function SurveyContextForm($form_type, $action, $survey_context, $user, $manager)
    {
        parent :: __construct('survey_context_form', 'post', $action);
              
        $this->survey_context = $survey_context;
        
        $this->user = $user;
        $this->form_type = $form_type;
        $this->manager = $manager;
        
        $this->build_header();
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->setDefaults();
        	$this->build_editing_form();
        }
        else
        {
            $this->build_creation_form();
        }
        
       
    }

    function build_header()
    {
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('SurveyContextRegistrationProperties') . '</span>');
    }

    function build_footer($action_name)
    {
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
        
        $buttons[] = $this->createElement('style_submit_button', 'create', Translation :: get($action_name), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/survey_context_registration_form.js'));
    }


    /**
     * Creates a new basic form
     */
    function build_creation_form()
    {
       
    	$survey_context = $this->survey_context;
     	$property_names = $survey_context->get_additional_property_names();
		
        foreach ($property_names as $property_name)
        {
            $this->add_textfield($property_name, $property_name, true);
        }
      
    	
       $this->build_footer('Create');
    }

    /**
     * Builds an editing form
     */
    function build_editing_form()
    {
    	
    	$survey_context = $this->survey_context;
     	$property_names = $survey_context->get_additional_property_names();
		
        foreach ($property_names as $property_name)
        {
            $this->add_textfield($property_name, $property_name, true);
        }
    	$this->addElement('hidden', SurveyContext :: PROPERTY_ID);
        $this->build_footer('Update');
    }

    function create_survey_context()
    {
        $survey_context = $this->survey_context;
     	$property_names = $survey_context->get_additional_property_names();
		
        foreach ($property_names as $property_name)
        {
            $survey_context->set_additional_property($property_name,$this->exportValue($property_name) );
        }

        return $survey_context->create();
    }

    function update_survey_context()
    {
        $survey_context = $this->survey_context;
     	$property_names = $survey_context->get_additional_property_names();
		
        foreach ($property_names as $property_name)
        {
            $survey_context->set_additional_property($property_name,$this->exportValue($property_name) );
        }
		        
        return $survey_context->update();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $survey_context = $this->survey_context;
    	$property_names = $survey_context->get_additional_property_names();
		
        foreach ($property_names as $property_name)
        {
            $defaults[$property_name] = $survey_context->get_additional_property($property_name);
        }

        parent :: setDefaults($defaults);
    }
      
}
?>