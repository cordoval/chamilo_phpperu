<?php
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'location_group.class.php';

/**
 * This class describes the form for a InternshipOrganizerLocationGroup object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class InternshipOrganizerLocationGroupForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $location_group;
    private $user;

    function InternshipOrganizerLocationGroupForm($form_type, $location_group, $action, $user)
    {
        parent :: __construct('location_group_settings', 'post', $action);
        
        $this->location_group = $location_group;
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
        $this->addElement('text', InternshipOrganizerLocationGroup :: PROPERTY_ID, Translation :: get('Id'));
        $this->addRule(InternshipOrganizerLocationGroup :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerLocationGroup :: PROPERTY_NAME, Translation :: get('Name'));
        $this->addRule(InternshipOrganizerLocationGroup :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
    
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        
        //$this->addElement('hidden', InternshipOrganizerLocationGroup :: PROPERTY_ID);
        

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

    function update_location_group()
    {
        $location_group = $this->location_group;
        $values = $this->exportValues();
        
        $location_group->set_id($values[InternshipOrganizerLocationGroup :: PROPERTY_ID]);
        $location_group->set_name($values[InternshipOrganizerLocationGroup :: PROPERTY_NAME]);
        
        return $location_group->update();
    }

    function create_location_group()
    {
        $location_group = $this->location_group;
        $values = $this->exportValues();
        
        $location_group->set_id($values[InternshipOrganizerLocationGroup :: PROPERTY_ID]);
        $location_group->set_name($values[InternshipOrganizerLocationGroup :: PROPERTY_NAME]);
        
        return $location_group->create();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $location_group = $this->location_group;
        
        $defaults[InternshipOrganizerLocationGroup :: PROPERTY_ID] = $location_group->get_id();
        $defaults[InternshipOrganizerLocationGroup :: PROPERTY_NAME] = $location_group->get_name();
        
        parent :: setDefaults($defaults);
    }
}
?>