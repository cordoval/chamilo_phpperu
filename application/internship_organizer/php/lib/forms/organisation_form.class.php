<?php
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'organisation.class.php';

/**
 * This class describes the form for a Place object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 * @author Steven Willaert
 **/
class InternshipOrganizerOrganisationForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $organisation;
    private $user;

    function InternshipOrganizerOrganisationForm($form_type, $organisation, $action, $user)
    {
        parent :: __construct('organisation_settings', 'post', $action);
        
        $this->organisation = $organisation;
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
        
        $this->addElement('text', InternshipOrganizerOrganisation :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
        $this->addRule(InternshipOrganizerOrganisation :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('textarea', InternshipOrganizerOrganisation :: PROPERTY_DESCRIPTION, Translation :: get('Description'), array("rows" => "5", "cols" => "40"));
        $this->addRule(InternshipOrganizerOrganisation :: PROPERTY_DESCRIPTION); /*, Translation :: get('ThisFieldIsRequired'), 'required');*/
    
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

    function update_organisation()
    {
        $organisation = $this->organisation;
        $values = $this->exportValues();
        
        $organisation->set_name($values[InternshipOrganizerOrganisation :: PROPERTY_NAME]);
        //    	$organisation->set_address($values[InternshipOrganizerOrganisation :: PROPERTY_ADDRESS]);
        //    	$organisation->set_postcode($values[InternshipOrganizerOrganisation :: PROPERTY_POSTCODE]);
        //    	$organisation->set_city($values[InternshipOrganizerOrganisation :: PROPERTY_CITY]);
        //    	$organisation->set_telephone($values[InternshipOrganizerOrganisation :: PROPERTY_TELEPHONE]);
        //    	$organisation->set_fax($values[InternshipOrganizerOrganisation :: PROPERTY_FAX]);
        //    	$organisation->set_email($values[InternshipOrganizerOrganisation :: PROPERTY_EMAIL]);
        $organisation->set_description($values[InternshipOrganizerOrganisation :: PROPERTY_DESCRIPTION]);
        
        return $organisation->update();
    }

    function create_organisation()
    {
        $organisation = $this->organisation;
        $values = $this->exportValues();
        
        $organisation->set_name($values[InternshipOrganizerOrganisation :: PROPERTY_NAME]);
        //   	$organisation->set_address($values[InternshipOrganizerOrganisation :: PROPERTY_ADDRESS]);
        //    	$organisation->set_postcode($values[InternshipOrganizerOrganisation :: PROPERTY_POSTCODE]);
        //    	$organisation->set_city($values[InternshipOrganizerOrganisation :: PROPERTY_CITY]);
        //    	$organisation->set_telephone($values[InternshipOrganizerOrganisation :: PROPERTY_TELEPHONE]);
        //    	$organisation->set_fax($values[InternshipOrganizerOrganisation :: PROPERTY_FAX]);
        //    	$organisation->set_email($values[InternshipOrganizerOrganisation :: PROPERTY_EMAIL]);
        $organisation->set_description($values[InternshipOrganizerOrganisation :: PROPERTY_DESCRIPTION]);
        
        return $organisation->create();
    }

    function get_organisation()
    {
        return $this->organisation;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $organisation = $this->organisation;
        
        $defaults[InternshipOrganizerOrganisation :: PROPERTY_NAME] = $organisation->get_name();
        //    	$defaults[InternshipOrganizerOrganisation :: PROPERTY_ADDRESS] = $organisation->get_address();
        //    	$defaults[InternshipOrganizerOrganisation :: PROPERTY_POSTCODE] = $organisation->get_postcode();
        //    	$defaults[InternshipOrganizerOrganisation :: PROPERTY_CITY] = $organisation->get_city();
        //    	$defaults[InternshipOrganizerOrganisation :: PROPERTY_TELEPHONE] = $organisation->get_telephone();
        //    	$defaults[InternshipOrganizerOrganisation :: PROPERTY_FAX] = $organisation->get_fax();
        //    	$defaults[InternshipOrganizerOrganisation :: PROPERTY_EMAIL] = $organisation->get_email();
        $defaults[InternshipOrganizerOrganisation :: PROPERTY_DESCRIPTION] = $organisation->get_description();
        
        parent :: setDefaults($defaults);
    }
}
?>