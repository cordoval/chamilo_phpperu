<?php
/**
 * $Id: rights_template_form.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.data_manager.forms
 */

class RightsTemplateForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'RightsTemplateUpdated';
    const RESULT_ERROR = 'RightsTemplateUpdateFailed';
    
    private $parent;
    private $rights_template;

    /**
     * Creates a new UserForm
     * Used by the admin to create/update a user
     */
    function RightsTemplateForm($form_type, $rights_template, $action)
    {
        parent :: __construct('rights_template_edit', 'post', $action);
        
        $this->rights_template = $rights_template;
        
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

    /**
     * Creates a basic form
     */
    function build_basic_form()
    {
        // Lastname
        $this->addElement('text', RightsTemplate :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
        $this->addRule(RightsTemplate :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_html_editor(RightsTemplate :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
    }

    /**
     * Creates an editing form
     */
    function build_editing_form()
    {
        $user = $this->user;
        $parent = $this->parent;
        
        $this->build_basic_form();
        
        $this->addElement('hidden', RightsTemplate :: PROPERTY_ID);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Creates a creating form
     */
    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Updates the user with the new data
     */
    function update_rights_template()
    {
        $rights_template = $this->rights_template;
        $values = $this->exportValues();
        
        $rights_template->set_name($values[RightsTemplate :: PROPERTY_NAME]);
        $rights_template->set_description($values[RightsTemplate :: PROPERTY_DESCRIPTION]);
        
        if (! $rights_template->update())
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Creates the user, and stores it in the database
     */
    function create_rights_template()
    {
        $rights_template = $this->rights_template;
        $values = $this->exportValues();
        
        $rights_template->set_name($values[RightsTemplate :: PROPERTY_NAME]);
        $rights_template->set_description($values[RightsTemplate :: PROPERTY_DESCRIPTION]);
        
        if (! $rights_template->create())
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $rights_template = $this->rights_template;
        
        $defaults[RightsTemplate :: PROPERTY_NAME] = $rights_template->get_name();
        $defaults[RightsTemplate :: PROPERTY_DESCRIPTION] = $rights_template->get_description();
        
        parent :: setDefaults($defaults);
    }
}
?>