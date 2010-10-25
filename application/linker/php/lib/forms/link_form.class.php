<?php
/**
 * $Id: link_form.class.php 199 2009-11-13 12:23:04Z chellee $
 * @package application.lib.linker.forms
 */
class LinkForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'LinkUpdated';
    const RESULT_ERROR = 'LinkUpdateFailed';
    
    private $link;
    private $user;

    function LinkForm($form_type, $link, $action, $user)
    {
        parent :: __construct('links_settings', 'post', $action);
        
        $this->link = $link;
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
        $this->addElement('text', Linker :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
        $this->addRule(Linker :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_html_editor(Linker :: PROPERTY_DESCRIPTION, Translation :: get('Description'), true);
        
        $this->addElement('text', Linker :: PROPERTY_URL, Translation :: get('Url'), array("size" => "50"));
        $this->addRule(Linker :: PROPERTY_URL, Translation :: get('ThisFieldIsRequired'), 'required');
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        
        $this->addElement('hidden', Linker :: PROPERTY_ID);
        
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

    function update_link()
    {
        $link = $this->link;
        $values = $this->exportValues();
        
        $link->set_name($values[Linker :: PROPERTY_NAME]);
        $link->set_description($values[Linker :: PROPERTY_DESCRIPTION]);
        $link->set_url($values[Linker :: PROPERTY_URL]);
        return $link->update();
    }

    function create_link()
    {
        $link = $this->link;
        $values = $this->exportValues();
        
        $link->set_name($values[Linker :: PROPERTY_NAME]);
        $link->set_description($values[Linker :: PROPERTY_DESCRIPTION]);
        $link->set_url($values[Linker :: PROPERTY_URL]);
        
        return $link->create();
    }

    /**
     * Sets default values. 
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $link = $this->link;
        $defaults[Linker :: PROPERTY_ID] = $link->get_id();
        $defaults[Linker :: PROPERTY_URL] = $link->get_url();
        $defaults[Linker :: PROPERTY_NAME] = $link->get_name();
        $defaults[Linker :: PROPERTY_DESCRIPTION] = $link->get_description();
        parent :: setDefaults($defaults);
    }
}
?>