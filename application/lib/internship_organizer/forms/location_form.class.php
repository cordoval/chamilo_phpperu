<?php
require_once dirname(__FILE__) . '/../location.class.php';

/**
 * This class describes the form for a InternshipOrganizerLocation object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class InternshipOrganizerLocationForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $location;
    private $user;

    function InternshipOrganizerLocationForm($form_type, $location, $action, $user)
    {
        parent :: __construct('location_settings', 'post', $action);
            
        $this->location = $location;
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
        
        $this->addElement('text', InternshipOrganizerLocation :: PROPERTY_NAME, Translation :: get('Name'));
        $this->addRule(InternshipOrganizerLocation :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerLocation :: PROPERTY_CITY, Translation :: get('City'));
        $this->addRule(InternshipOrganizerLocation :: PROPERTY_CITY, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerLocation :: PROPERTY_STREET, Translation :: get('Street'));
        $this->addRule(InternshipOrganizerLocation :: PROPERTY_STREET, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerLocation :: PROPERTY_STREET_NUMBER, Translation :: get('StreetNumber'));
        $this->addRule(InternshipOrganizerLocation :: PROPERTY_STREET_NUMBER, Translation :: get('ThisFieldIsRequired'), 'required');
        
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        
        //$this->addElement('hidden', InternshipOrganizerLocation :: PROPERTY_ID);
        

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

    function update_location()
    {
        $location = $this->location;
        $values = $this->exportValues();
        
        $location->set_name($values[InternshipOrganizerLocation :: PROPERTY_NAME]);
        $location->set_street($values[InternshipOrganizerLocation :: PROPERTY_STREET]);
        $location->set_street_number($values[InternshipOrganizerLocation :: PROPERTY_STREET_NUMBER]);
        $location->set_city($values[InternshipOrganizerLocation :: PROPERTY_CITY]);
                
        return $location->update();
    }

    function create_location()
    {
        $location = $this->location;
        $values = $this->exportValues();
        
        $location->set_name($values[InternshipOrganizerLocation :: PROPERTY_NAME]);
        $location->set_street($values[InternshipOrganizerLocation :: PROPERTY_STREET]);
        $location->set_street_number($values[InternshipOrganizerLocation :: PROPERTY_STREET_NUMBER]);
        $location->set_city($values[InternshipOrganizerLocation :: PROPERTY_CITY]);
	     
        return $location->create();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $location = $this->location;
        
        $defaults[InternshipOrganizerLocation :: PROPERTY_NAME] = $location->get_name();
        $defaults[InternshipOrganizerLocation :: PROPERTY_STREET] = $location->get_street();
        $defaults[InternshipOrganizerLocation :: PROPERTY_STREET_NUMBER] = $location->get_street_number();
        $defaults[InternshipOrganizerLocation :: PROPERTY_CITY] = $location->get_city();
        
        parent :: setDefaults($defaults);
    }
}
?>