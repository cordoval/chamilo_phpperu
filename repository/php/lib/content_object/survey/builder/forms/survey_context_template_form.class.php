<?php

class SurveyContextTemplateForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'SurveyContextTemplateUpdated';
    const RESULT_ERROR = 'SurveyContextTemplateUpdateFailed';
    
    private $parent;
    private $template;
   	private $user;

    function SurveyContextTemplateForm($form_type, $template, $action, $user)
    {
        parent :: __construct('create_context_template', 'post', $action);
        
        $this->template = $template;
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
        $this->addElement('text', SurveyContextTemplate :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
        $this->addRule(SurveyContextTemplate :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
               
        $this->addElement('select', SurveyContextTemplate :: PROPERTY_PARENT_ID, Translation :: get('Category'), $this->get_templates());
        $this->addRule(SurveyContextTemplate :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_html_editor(SurveyContextTemplate :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
    
    }

    function build_editing_form()
    {
        $template = $this->template;
        $parent = $this->parent;
        
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

    function update_template()
    {
        $template = $this->template;
        $values = $this->exportValues();
        
        $template->set_name($values[SurveyContextTemplate :: PROPERTY_NAME]);
        $template->set_description($values[SurveyContextTemplate :: PROPERTY_DESCRIPTION]);
        $value = $template->update();
        
        $new_parent = $values[SurveyContextTemplate :: PROPERTY_PARENT_ID];
        if ($template->get_parent_id() != $new_parent)
        {
            $template->move($new_parent);
        }
               
//        if ($value)
//        {
//            Event :: trigger('update', 'template', array('target_template_id' => $template->get_id(), 'action_user_id' => $this->user->get_id()));
//        }
        
        return $value;
    }

    function create_template()
    {
        $template = $this->template;
        $values = $this->exportValues();
        
        $template->set_name($values[SurveyContextTemplate :: PROPERTY_NAME]);
        $template->set_description($values[SurveyContextTemplate :: PROPERTY_DESCRIPTION]);
        $template->set_parent_id($values[SurveyContextTemplate :: PROPERTY_PARENT_ID]);
        
        $value = $template->create();
               
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
        $template = $this->template;
        $defaults[SurveyContextTemplate :: PROPERTY_ID] = $template->get_id();
        $defaults[SurveyContextTemplate :: PROPERTY_PARENT_ID] = $template->get_parent_id();
        $defaults[SurveyContextTemplate :: PROPERTY_NAME] = $template->get_name();
        $defaults[SurveyContextTemplate :: PROPERTY_DESCRIPTION] = $template->get_description();
        parent :: setDefaults($defaults);
    }

    function get_template()
    {
        return $this->template;
    }

    function get_templates()
    {
        $template = $this->template;
        
        $template_menu = new SurveyContextTemplateMenu($template->get_id(), null, true, true, true);
        $renderer = new OptionsMenuRenderer();
        $template_menu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }
}
?>