<?php
require_once dirname(__FILE__) . '/../location.class.php';

/**
 * This class describes the form for a InternshipLocation object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class InternshipLocationForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $location;
    private $user;

    function InternshipLocationForm($form_type, $location, $action, $user)
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
        
        $this->addElement('text', InternshipLocation :: PROPERTY_NAME, Translation :: get('Name'));
        $this->addRule(InternshipLocation :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipLocation :: PROPERTY_STREET, Translation :: get('Street'));
        $this->addRule(InternshipLocation :: PROPERTY_STREET, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipLocation :: PROPERTY_STREET_NUMBER, Translation :: get('StreetNumber'));
        $this->addRule(InternshipLocation :: PROPERTY_STREET_NUMBER, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipLocation :: PROPERTY_PLACE_ID, Translation :: get('PlaceId'));
        $this->addRule(InternshipLocation :: PROPERTY_PLACE_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipLocation :: PROPERTY_LOCATION_GROUP_ID, Translation :: get('InternshipLocationGroupId'));
        $this->addRule(InternshipLocation :: PROPERTY_LOCATION_GROUP_ID, Translation :: get('ThisFieldIsRequired'), 'required');
    
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        
        //$this->addElement('hidden', InternshipLocation :: PROPERTY_ID);
        

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
        
        $location->set_name($values[InternshipLocation :: PROPERTY_NAME]);
        $location->set_street($values[InternshipLocation :: PROPERTY_STREET]);
        $location->set_street_number($values[InternshipLocation :: PROPERTY_STREET_NUMBER]);
        $location->set_place_id($values[InternshipLocation :: PROPERTY_PLACE_ID]);
        $location->set_location_group_id($values[InternshipLocation :: PROPERTY_LOCATION_GROUP_ID]);
        
        return $location->update();
    }

    function create_location()
    {
        $location = $this->location;
        $values = $this->exportValues();
        
        $location->set_name($values[InternshipLocation :: PROPERTY_NAME]);
        $location->set_street($values[InternshipLocation :: PROPERTY_STREET]);
        $location->set_street_number($values[InternshipLocation :: PROPERTY_STREET_NUMBER]);
        $location->set_place_id($values[InternshipLocation :: PROPERTY_PLACE_ID]);
        $location->set_location_group_id($values[InternshipLocation :: PROPERTY_LOCATION_GROUP_ID]);
        
        return $location->create();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $location = $this->location;
        
        $defaults[InternshipLocation :: PROPERTY_NAME] = $location->get_name();
        $defaults[InternshipLocation :: PROPERTY_STREET] = $location->get_street();
        $defaults[InternshipLocation :: PROPERTY_STREET_NUMBER] = $location->get_street_number();
        $defaults[InternshipLocation :: PROPERTY_PLACE_ID] = $location->get_place_id();
        $defaults[InternshipLocation :: PROPERTY_LOCATION_GROUP_ID] = $location->get_location_group_id();
        
        parent :: setDefaults($defaults);
    }
}
?>