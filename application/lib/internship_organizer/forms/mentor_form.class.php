<?php
require_once dirname(__FILE__) . '/../mentor.class.php';

/**
 * This class describes the form for a Mentor object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class InternshipOrganizerMentorForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $mentor;
    private $user;

    function InternshipOrganizerMentorForm($form_type, $mentor, $action, $user)
    {
        parent :: __construct('mentor_settings', 'post', $action);
        
        $this->mentor = $mentor;
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
        $this->addElement('text', InternshipOrganizerMentor :: PROPERTY_TITLE, Translation :: get('Title'));
        
        $this->addElement('text', InternshipOrganizerMentor :: PROPERTY_FIRSTNAME, Translation :: get('Firstname'));
        $this->addRule(InternshipOrganizerMentor :: PROPERTY_FIRSTNAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerMentor :: PROPERTY_LASTNAME, Translation :: get('Lastname'));
        $this->addRule(InternshipOrganizerMentor :: PROPERTY_LASTNAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', InternshipOrganizerMentor :: PROPERTY_EMAIL, Translation :: get('Email'));
        
        $this->addElement('text', InternshipOrganizerMentor :: PROPERTY_TELEPHONE, Translation :: get('Telephone'));
        
        $this->addElement('text', InternshipOrganizerMentor :: PROPERTY_USER_ID, Translation :: get('UserId'));
    
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
        $mentor->set_user_id($values[InternshipOrganizerMentor :: PROPERTY_USER_ID]);
        
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
        $mentor->set_user_id($values[InternshipOrganizerMentor :: PROPERTY_USER_ID]);
        
        return $mentor->create();
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
        $defaults[InternshipOrganizerMentor :: PROPERTY_USER_ID] = $mentor->get_user_id();
        
        parent :: setDefaults($defaults);
    }
}
?>