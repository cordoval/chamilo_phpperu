<?php
require_once dirname(__FILE__) . '/../location_rel_moment.class.php';

/**
 * This class describes the form for a InternshipOrganizerLocationRelMoment object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class InternshipOrganizerLocationRelMomentForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $location_rel_moment;
    private $user;

    function InternshipOrganizerLocationRelMomentForm($form_type, $location_rel_moment, $action, $user)
    {
        parent :: __construct('location_rel_moment_settings', 'post', $action);
        
        $this->location_rel_moment = $location_rel_moment;
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
        $this->addElement('text', InternshipOrganizerLocationRelMoment :: PROPERTY_ID, Translation :: get('Id'));
        $this->addRule(InternshipOrganizerLocationRelMoment :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerLocationRelMoment :: PROPERTY_MOMENT_ID, Translation :: get('MomentId'));
        $this->addRule(InternshipOrganizerLocationRelMoment :: PROPERTY_MOMENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerLocationRelMoment :: PROPERTY_LOCATION_ID, Translation :: get('InternshipOrganizerLocationId'));
        $this->addRule(InternshipOrganizerLocationRelMoment :: PROPERTY_LOCATION_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerLocationRelMoment :: PROPERTY_MENTOR_ID, Translation :: get('MentorId'));
        $this->addRule(InternshipOrganizerLocationRelMoment :: PROPERTY_MENTOR_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerLocationRelMoment :: PROPERTY_STATUS, Translation :: get('Status'));
        $this->addRule(InternshipOrganizerLocationRelMoment :: PROPERTY_STATUS, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerLocationRelMoment :: PROPERTY_PRIORITY, Translation :: get('Priority'));
        $this->addRule(InternshipOrganizerLocationRelMoment :: PROPERTY_PRIORITY, Translation :: get('ThisFieldIsRequired'), 'required');
    
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        
        //$this->addElement('hidden', InternshipOrganizerLocationRelMoment :: PROPERTY_ID);
        

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

    function update_location_rel_moment()
    {
        $location_rel_moment = $this->location_rel_moment;
        $values = $this->exportValues();
        
        $location_rel_moment->set_id($values[InternshipOrganizerLocationRelMoment :: PROPERTY_ID]);
        $location_rel_moment->set_moment_id($values[InternshipOrganizerLocationRelMoment :: PROPERTY_MOMENT_ID]);
        $location_rel_moment->set_location_id($values[InternshipOrganizerLocationRelMoment :: PROPERTY_LOCATION_ID]);
        $location_rel_moment->set_mentor_id($values[InternshipOrganizerLocationRelMoment :: PROPERTY_MENTOR_ID]);
        $location_rel_moment->set_status($values[InternshipOrganizerLocationRelMoment :: PROPERTY_STATUS]);
        $location_rel_moment->set_priority($values[InternshipOrganizerLocationRelMoment :: PROPERTY_PRIORITY]);
        
        return $location_rel_moment->update();
    }

    function create_location_rel_moment()
    {
        $location_rel_moment = $this->location_rel_moment;
        $values = $this->exportValues();
        
        $location_rel_moment->set_id($values[InternshipOrganizerLocationRelMoment :: PROPERTY_ID]);
        $location_rel_moment->set_moment_id($values[InternshipOrganizerLocationRelMoment :: PROPERTY_MOMENT_ID]);
        $location_rel_moment->set_location_id($values[InternshipOrganizerLocationRelMoment :: PROPERTY_LOCATION_ID]);
        $location_rel_moment->set_mentor_id($values[InternshipOrganizerLocationRelMoment :: PROPERTY_MENTOR_ID]);
        $location_rel_moment->set_status($values[InternshipOrganizerLocationRelMoment :: PROPERTY_STATUS]);
        $location_rel_moment->set_priority($values[InternshipOrganizerLocationRelMoment :: PROPERTY_PRIORITY]);
        
        return $location_rel_moment->create();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $location_rel_moment = $this->location_rel_moment;
        
        $defaults[InternshipOrganizerLocationRelMoment :: PROPERTY_ID] = $location_rel_moment->get_id();
        $defaults[InternshipOrganizerLocationRelMoment :: PROPERTY_MOMENT_ID] = $location_rel_moment->get_moment_id();
        $defaults[InternshipOrganizerLocationRelMoment :: PROPERTY_LOCATION_ID] = $location_rel_moment->get_location_id();
        $defaults[InternshipOrganizerLocationRelMoment :: PROPERTY_MENTOR_ID] = $location_rel_moment->get_mentor_id();
        $defaults[InternshipOrganizerLocationRelMoment :: PROPERTY_STATUS] = $location_rel_moment->get_status();
        $defaults[InternshipOrganizerLocationRelMoment :: PROPERTY_PRIORITY] = $location_rel_moment->get_priority();
        
        parent :: setDefaults($defaults);
    }
}
?>