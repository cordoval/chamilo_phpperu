<?php
require_once dirname(__FILE__) . '/../location.class.php';

/**
 * This class describes the form for a InternshipOrganizerLocation object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 * @author Steven Willaert
 **/
class InternshipOrganizerLocationForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $location;
    private $user;
    private $region_id;

    function InternshipOrganizerLocationForm($form_type, $location, $action, $user)
    {
        parent :: __construct('location_settings', 'post', $action);
        
        $this->location = $location;
        $this->user = $user;
        $this->region_id = $location->get_region_id();
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
        
        $this->addElement('text', InternshipOrganizerLocation :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
        $this->addRule(InternshipOrganizerLocation :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerLocation :: PROPERTY_ADDRESS, Translation :: get('Address'), array("size" => "50"));
        $this->addRule(InternshipOrganizerLocation :: PROPERTY_ADDRESS, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('select', InternshipOrganizerLocation :: PROPERTY_REGION_ID, Translation :: get('City'), $this->get_regions());
        $this->addRule(InternshipOrganizerLocation :: PROPERTY_REGION_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
        //$this->addElement('text', InternshipOrganizerLocation :: PROPERTY_POSTCODE, Translation :: get('Postcode'), array("size" => "6"));
        //$this->addRule(InternshipOrganizerLocation :: PROPERTY_POSTCODE, Translation :: get('ThisFieldIsRequired'), 'required');
        

        //$this->addElement('text', InternshipOrganizerLocation :: PROPERTY_CITY, Translation :: get('City'), array("size" => "50"));
        //$this->addRule(InternshipOrganizerLocation :: PROPERTY_CITY, Translation :: get('ThisFieldIsRequired'), 'required');
        

        $this->addElement('text', InternshipOrganizerLocation :: PROPERTY_TELEPHONE, Translation :: get('Telephone'), array("size" => "20"));
        $this->addRule(InternshipOrganizerLocation :: PROPERTY_TELEPHONE, Translation :: get('ThisFieldIsRequired'));
        
        $this->addElement('text', InternshipOrganizerLocation :: PROPERTY_FAX, Translation :: get('Fax'), array("size" => "20"));
        $this->addRule(InternshipOrganizerLocation :: PROPERTY_FAX, Translation :: get('ThisFieldIsRequired'));
        
        $this->addElement('text', InternshipOrganizerLocation :: PROPERTY_EMAIL, Translation :: get('Email'), array("size" => "50"));
        $this->addRule(InternshipOrganizerLocation :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired'));
        
        $this->addElement('textarea', InternshipOrganizerLocation :: PROPERTY_DESCRIPTION, Translation :: get('Description'), array("rows" => "5", "cols" => "17"));
        $this->addRule(InternshipOrganizerLocation :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'));
    
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
        $location->set_address($values[InternshipOrganizerLocation :: PROPERTY_ADDRESS]);
        $location->set_region_id($values[InternshipOrganizerLocation :: PROPERTY_REGION_ID]);
        //$location->set_postcode($values[InternshipOrganizerLocation :: PROPERTY_POSTCODE]);
        //$location->set_city($values[InternshipOrganizerLocation :: PROPERTY_CITY]);
        $location->set_telephone($values[InternshipOrganizerLocation :: PROPERTY_TELEPHONE]);
        $location->set_fax($values[InternshipOrganizerLocation :: PROPERTY_FAX]);
        $location->set_email($values[InternshipOrganizerLocation :: PROPERTY_EMAIL]);
        $location->set_description($values[InternshipOrganizerLocation :: PROPERTY_DESCRIPTION]);
        
        return $location->update();
    }

    function create_location()
    {
        $location = $this->location;
        $values = $this->exportValues();
        
        $location->set_name($values[InternshipOrganizerLocation :: PROPERTY_NAME]);
        $location->set_address($values[InternshipOrganizerLocation :: PROPERTY_ADDRESS]);
        $location->set_region_id($values[InternshipOrganizerLocation :: PROPERTY_REGION_ID]);
        //$location->set_postcode($values[InternshipOrganizerLocation :: PROPERTY_POSTCODE]);
        //$location->set_city($values[InternshipOrganizerLocation :: PROPERTY_CITY]);
        $location->set_telephone($values[InternshipOrganizerLocation :: PROPERTY_TELEPHONE]);
        $location->set_fax($values[InternshipOrganizerLocation :: PROPERTY_FAX]);
        $location->set_email($values[InternshipOrganizerLocation :: PROPERTY_EMAIL]);
        $location->set_description($values[InternshipOrganizerLocation :: PROPERTY_DESCRIPTION]);
        
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
        $defaults[InternshipOrganizerLocation :: PROPERTY_ADDRESS] = $location->get_address();
        $defaults[InternshipOrganizerLocation :: PROPERTY_REGION_ID] = $location->get_region_id();
        //$defaults[InternshipOrganizerLocation :: PROPERTY_POSTCODE] = $location->get_postcode();
        //$defaults[InternshipOrganizerLocation :: PROPERTY_CITY] = $location->get_city();
        $defaults[InternshipOrganizerLocation :: PROPERTY_TELEPHONE] = $location->get_telephone();
        $defaults[InternshipOrganizerLocation :: PROPERTY_FAX] = $location->get_fax();
        $defaults[InternshipOrganizerLocation :: PROPERTY_EMAIL] = $location->get_email();
        $defaults[InternshipOrganizerLocation :: PROPERTY_DESCRIPTION] = $location->get_description();
        
        parent :: setDefaults($defaults);
    }

    function get_regions()
    {
        $region_id = $this->region_id;
        
        $region_menu = new InternshipOrganizerRegionMenu($this->region_id, null, false, true, false);
        $renderer = new OptionsMenuRenderer();
        $region_menu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }
}
?>