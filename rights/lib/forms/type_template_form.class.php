<?php
/**
 * $Id: type_template_form.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.data_manager.forms
 */

class TypeTemplateForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'TypeTemplateUpdated';
    const RESULT_ERROR = 'TypeTemplateUpdateFailed';
    
    private $parent;
    private $type_template;

    /**
     * Creates a new UserForm
     * Used by the admin to create/update a user
     */
    function TypeTemplateForm($form_type, $type_template, $action)
    {
        parent :: __construct('type_template_edit', 'post', $action);
        
        $this->type_template = $type_template;
        
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
        $this->addElement('text', TypeTemplate :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
        $this->addRule(TypeTemplate :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_html_editor(TypeTemplate :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
    }

    /**
     * Creates an editing form
     */
    function build_editing_form()
    {
        $user = $this->user;
        $parent = $this->parent;
        
        $this->build_basic_form();
        
        $this->addElement('hidden', TypeTemplate :: PROPERTY_ID);
        
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
    function update_type_template()
    {
        $type_template = $this->type_template;
        $values = $this->exportValues();
        
        $type_template->set_name($values[TypeTemplate :: PROPERTY_NAME]);
        $type_template->set_description($values[TypeTemplate :: PROPERTY_DESCRIPTION]);
        
        if (! $type_template->update())
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
    function create_type_template()
    {
        $type_template = $this->type_template;
        $values = $this->exportValues();
        
        $type_template->set_name($values[TypeTemplate :: PROPERTY_NAME]);
        $type_template->set_description($values[TypeTemplate :: PROPERTY_DESCRIPTION]);
        
        if (! $type_template->create())
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
        $type_template = $this->type_template;
        
        $defaults[TypeTemplate :: PROPERTY_NAME] = $type_template->get_name();
        $defaults[TypeTemplate :: PROPERTY_DESCRIPTION] = $type_template->get_description();
        
        parent :: setDefaults($defaults);
    }
}
?>