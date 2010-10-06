<?php
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'appointment.class.php';

class InternshipOrganizerAppointmentForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $form_type;
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
        
        $this->add_select(InternshipOrganizerAppointment :: PROPERTY_STATUS, Translation :: get('Status'), InternshipOrganizerAppointment :: get_states(), true);
        
        $this->add_select(InternshipOrganizerAppointment :: PROPERTY_TYPE, Translation :: get('Type'), InternshipOrganizerAppointment :: get_types(), true );
    
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
        
        $appointment->set_title($values[InternshipOrganizerAppointment :: PROPERTY_TITLE]);
        $appointment->set_description($values[InternshipOrganizerAppointment :: PROPERTY_DESCRIPTION]);
        $appointment->set_status($values[InternshipOrganizerAppointment :: PROPERTY_STATUS]);
        $appointment->set_type($values[InternshipOrganizerAppointment :: PROPERTY_TYPE]);
        
        return $appointment->update();
    }

    function create_appointment()
    {
        $appointment = $this->appointment;
        $values = $this->exportValues();
        
        $appointment->set_title($values[InternshipOrganizerAppointment :: PROPERTY_TITLE]);
        $appointment->set_description($values[InternshipOrganizerAppointment :: PROPERTY_DESCRIPTION]);
        $appointment->set_status($values[InternshipOrganizerAppointment :: PROPERTY_STATUS]);
        $appointment->set_type($values[InternshipOrganizerAppointment :: PROPERTY_TYPE]);
		$appointment->set_created(time());
        		
        return $appointment->create();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $appointment = $this->appointment;
        
        $defaults[InternshipOrganizerAppointment :: PROPERTY_TITLE] = $appointment->get_title();
        $defaults[InternshipOrganizerAppointment :: PROPERTY_DESCRIPTION] = $appointment->get_description();
        $defaults[InternshipOrganizerAppointment :: PROPERTY_STATUS] = $appointment->get_status();
        $defaults[InternshipOrganizerAppointment :: PROPERTY_TYPE] = $appointment->get_type();
        
        if($this->form_type == self :: TYPE_CREATE){
        	$defaults[InternshipOrganizerAppointment :: PROPERTY_STATUS] = InternshipOrganizerAppointment :: STATUS_CONFIRMED;
        	$defaults[InternshipOrganizerAppointment :: PROPERTY_TYPE] = InternshipOrganizerAppointment :: TYPE_VISIT;
        }
        
        parent :: setDefaults($defaults);
    }
}
?>