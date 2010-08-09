<?php
require_once dirname(__FILE__) . '/../mentor.class.php';
require_once dirname(__FILE__) . '/../mentor_rel_user.class.php';

/**
 * This class describes the form for a Mentor object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class InternshipOrganizerMentorForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const PARAM_TARGET_LOCATIONS = 'target_locations';
    const PARAM_TARGET_USERS = 'target_users';
    
    private $mentor;
    private $user;
    private $organisation_id;

    function InternshipOrganizerMentorForm($form_type, $mentor, $action, $user, $organisation_id)
    {
        parent :: __construct('mentor_settings', 'post', $action);
        
        $this->mentor = $mentor;
        $this->user = $user;
        $this->form_type = $form_type;
        $this->organisation_id = $organisation_id;
        
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
        $this->addElement('text', InternshipOrganizerMentor :: PROPERTY_TITLE, Translation :: get('Title'));
        
        $this->addElement('text', InternshipOrganizerMentor :: PROPERTY_FIRSTNAME, Translation :: get('Firstname'));
        $this->addRule(InternshipOrganizerMentor :: PROPERTY_FIRSTNAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerMentor :: PROPERTY_LASTNAME, Translation :: get('Lastname'));
        $this->addRule(InternshipOrganizerMentor :: PROPERTY_LASTNAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerMentor :: PROPERTY_EMAIL, Translation :: get('Email'));
        
        $this->addElement('text', InternshipOrganizerMentor :: PROPERTY_TELEPHONE, Translation :: get('Telephone'));
        
        $url = Path :: get(WEB_PATH) . 'application/lib/internship_organizer/xml_feeds/xml_location_feed.php?organisation_id=' . $this->organisation_id;
        
        $locale = array();
        $locale['Display'] = Translation :: get('ChooseUsers');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $elem = $this->addElement('element_finder', self :: PARAM_TARGET_LOCATIONS, Translation :: get('Locations'), $url, $locale, array());
        $defaults = array();
        $elem->setDefaults($defaults);
        $elem->setDefaultCollapsed(false);
        
        $url = Path :: get(WEB_PATH) . 'application/lib/internship_organizer/xml_feeds/xml_organisation_user_feed.php?organisation_id=' . $this->organisation_id;
        $locale = array();
        $locale['Display'] = Translation :: get('ChooseUsers');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $elem = $this->addElement('element_finder', self :: PARAM_TARGET_USERS, Translation :: get('Users'), $url, $locale, array());
        $defaults = array();
        $elem->setDefaults($defaults);
        $elem->setDefaultCollapsed(false);
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        
        //$this->addElement('hidden', InternshipOrganizerMentor :: PROPERTY_ID);
        

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

    function update_mentor()
    {
        $mentor = $this->mentor;
        $values = $this->exportValues();
        
        $mentor->set_id($values[InternshipOrganizerMentor :: PROPERTY_ID]);
        $mentor->set_title($values[InternshipOrganizerMentor :: PROPERTY_TITLE]);
        $mentor->set_firstname($values[InternshipOrganizerMentor :: PROPERTY_FIRSTNAME]);
        $mentor->set_lastname($values[InternshipOrganizerMentor :: PROPERTY_LASTNAME]);
        $mentor->set_email($values[InternshipOrganizerMentor :: PROPERTY_EMAIL]);
        $mentor->set_telephone($values[InternshipOrganizerMentor :: PROPERTY_TELEPHONE]);
        
        return $mentor->update();
    }

    function create_mentor()
    {
        $mentor = $this->mentor;
        $values = $this->exportValues();
        
        $mentor->set_id($values[InternshipOrganizerMentor :: PROPERTY_ID]);
        $mentor->set_title($values[InternshipOrganizerMentor :: PROPERTY_TITLE]);
        $mentor->set_firstname($values[InternshipOrganizerMentor :: PROPERTY_FIRSTNAME]);
        $mentor->set_lastname($values[InternshipOrganizerMentor :: PROPERTY_LASTNAME]);
        $mentor->set_email($values[InternshipOrganizerMentor :: PROPERTY_EMAIL]);
        $mentor->set_telephone($values[InternshipOrganizerMentor :: PROPERTY_TELEPHONE]);
        
        $value = $mentor->create();
        
        if ($value)
        {
            
            $mentor_id = $mentor->get_id();
            $users = $values[self :: PARAM_TARGET_USERS]['user'];
            
            foreach ($users as $user_id)
            {
                $mentor_rel_user = new InternshipOrganizerMentorRelUser();
                $mentor_rel_user->set_mentor_id($mentor_id);
                $mentor_rel_user->set_user_id($user_id);
                $mentor_rel_user->create();
            }
            
            $locations = $values[self :: PARAM_TARGET_LOCATIONS]['location'];
            
            foreach ($locations as $location_id)
            {
                $mentor_rel_location = new InternshipOrganizerMentorRelLocation();
                $mentor_rel_location->set_mentor_id($mentor_id);
                $mentor_rel_location->set_location_id($location_id);
                $mentor_rel_location->create();
            }
        
        }
        
        return $value;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $mentor = $this->mentor;
        
        $defaults[InternshipOrganizerMentor :: PROPERTY_ID] = $mentor->get_id();
        $defaults[InternshipOrganizerMentor :: PROPERTY_TITLE] = $mentor->get_title();
        $defaults[InternshipOrganizerMentor :: PROPERTY_FIRSTNAME] = $mentor->get_firstname();
        $defaults[InternshipOrganizerMentor :: PROPERTY_LASTNAME] = $mentor->get_lastname();
        $defaults[InternshipOrganizerMentor :: PROPERTY_EMAIL] = $mentor->get_email();
        $defaults[InternshipOrganizerMentor :: PROPERTY_TELEPHONE] = $mentor->get_telephone();
        
        parent :: setDefaults($defaults);
    }

}
?>