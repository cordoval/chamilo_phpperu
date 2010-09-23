<?php
require_once dirname(__FILE__) . '/../appointment.class.php';

class InternshipOrganizerAppointmentForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $appointment;
    private $user;

    function InternshipOrganizerAppointmentForm($form_type, $appointment, $action, $user)
    {
        parent :: __construct('appointment_settings', 'post', $action);
        
        $this->appointment = $appointment;
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
        
        $this->addElement('text', InternshipOrganizerAppointment :: PROPERTY_TITLE, Translation :: get('Title'));
        $this->addRule(InternshipOrganizerAppointment :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerAppointment :: PROPERTY_DESCRIPTION, Translation :: get('Description'));
        $this->addRule(InternshipOrganizerAppointment :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_datepicker(InternshipOrganizerAppointment :: PROPERTY_BEGIN, Translation :: get('Begin'));
        $this->addRule(InternshipOrganizerAppointment :: PROPERTY_BEGIN, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_datepicker(InternshipOrganizerAppointment :: PROPERTY_END, Translation :: get('End'));
        $this->addRule(InternshipOrganizerAppointment :: PROPERTY_END, Translation :: get('ThisFieldIsRequired'), 'required');
    
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        
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

    function update_appointment()
    {
        $appointment = $this->appointment;
        $values = $this->exportValues();
        
        $appointment->set_name($values[InternshipOrganizerAppointment :: PROPERTY_NAME]);
        $appointment->set_description($values[InternshipOrganizerAppointment :: PROPERTY_DESCRIPTION]);
        $appointment->set_begin(Utilities :: time_from_datepicker($values[InternshipOrganizerPeriod :: PROPERTY_BEGIN]));
        $appointment->set_end(Utilities :: time_from_datepicker($values[InternshipOrganizerPeriod :: PROPERTY_END]));
        
        return $appointment->update();
    }

    function create_appointment()
    {
        $appointment = $this->appointment;
        $values = $this->exportValues();
        
        $appointment->set_name($values[InternshipOrganizerAppointment :: PROPERTY_NAME]);
        $appointment->set_description($values[InternshipOrganizerAppointment :: PROPERTY_DESCRIPTION]);
        
        $appointment->set_begin(Utilities :: time_from_datepicker($values[InternshipOrganizerPeriod :: PROPERTY_BEGIN]));
        $appointment->set_end(Utilities :: time_from_datepicker($values[InternshipOrganizerPeriod :: PROPERTY_END]));
              
        return $appointment->create();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $appointment = $this->appointment;
        
        $defaults[InternshipOrganizerAppointment :: PROPERTY_NAME] = $appointment->get_name();
        $defaults[InternshipOrganizerAppointment :: PROPERTY_DESCRIPTION] = $appointment->get_description();
        $defaults[InternshipOrganizerAppointment :: PROPERTY_BEGIN] = $appointment->get_begin();
        $defaults[InternshipOrganizerAppointment :: PROPERTY_END] = $appointment->get_end();
        
        parent :: setDefaults($defaults);
    }
}
?>