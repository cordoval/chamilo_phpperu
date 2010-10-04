<?php
/**
 * $Id: reporting_template_registration_form.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.forms
 * @author Michael Kyndt
 */

class ReportingTemplateRegistrationForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ReportingTemplateRegistrationUpdated';
    const RESULT_ERROR = 'ReportingTemplateRegistrationUpdateFailed';
    
    private $parent;
    private $reporting_template_registration;

    /**
     * Creates a new UserForm
     * Used by the admin to create/update a user
     */
    function ReportingTemplateRegistrationForm($form_type, $reporting_template_registration, $action)
    {
        parent :: __construct('reportingtemplateregistration_edit', 'post', $action);
        
        $this->reporting_template_registration = $reporting_template_registration;
        
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
        $this->addElement('text', ReportingTemplateRegistration :: PROPERTY_TITLE, Translation :: get('Title'), array("size" => "50"));
        $this->addRule(ReportingTemplateRegistration :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_html_editor(ReportingTemplateRegistration :: PROPERTY_DESCRIPTION, Translation :: get('Description'), true);
    }

    /**
     * Creates an editing form
     */
    function build_editing_form()
    {
        $user = $this->user;
        $parent = $this->parent;
        
        $this->build_basic_form();
        
        $this->addElement('hidden', ReportingTemplateRegistration :: PROPERTY_ID);
        
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
     * Updates the reporting template registration with the new data
     */
    function update_reporting_template_registration()
    {
        $reporting_template_registration = $this->reporting_template_registration;
        $values = $this->exportValues();
        
        $reporting_template_registration->set_title($values[ReportingTemplateRegistration :: PROPERTY_TITLE]);
        $reporting_template_registration->set_description($values[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION]);
        
        //        return $reporting_template_registration->update();
        if (! $reporting_template_registration->update())
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
    function create_reporting_template_registration()
    {
        $reporting_template_registration = $this->reporting_template_registration;
        $values = $this->exportValues();
        
        $reporting_template_registration->set_title($values[Role :: PROPERTY_TITLE]);
        $reporting_template_registration->set_description($values[Role :: PROPERTY_DESCRIPTION]);
        
        //        return $reporting_template_registration->create();
        if (! $reporting_template_registration->create())
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
        $reporting_template_registration = $this->reporting_template_registration;
        
        $defaults[ReportingTemplateRegistration :: PROPERTY_TITLE] = $reporting_template_registration->get_title();
        $defaults[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = $reporting_template_registration->get_description();
        
        parent :: setDefaults($defaults);
    }
}
?>