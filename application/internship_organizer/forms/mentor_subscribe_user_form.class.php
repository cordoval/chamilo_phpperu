<?php

class InternshipOrganizerMentorSubscribeUserForm extends FormValidator
{
    
    const APPLICATION_NAME = 'internship_organizer';
    const PARAM_TARGET = 'users';
    
    private $mentor;
    private $user;

    function InternshipOrganizerMentorSubscribeUserForm($mentor, $action, $user)
    {
        parent :: __construct('create_mentor_rel_user', 'post', $action);
        
        $this->mentor = $mentor;
        $this->user = $user;
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $mentor = $this->mentor;
     
        $url = Path :: get(WEB_PATH) . 'application/lib/internship_organizer/xml_feeds/xml_organisation_user_feed.php?organisation_id=' . $mentor->get_organisation_id();
             
        $locale = array();
        $locale['Display'] = Translation :: get('ChooseUsers');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $elem = $this->addElement('element_finder', self :: PARAM_TARGET, Translation :: get('InternshipOrganizerUsers'), $url, $locale, array(), array('load_elements' => true));
        $defaults = array();
        $elem->setDefaults($defaults);
        $elem->setDefaultCollapsed(false);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function create_mentor_rel_user()
    {
        $mentor_id = $this->mentor->get_id();
        
        $values = $this->exportValues();
        
        $user_ids = $values[self :: PARAM_TARGET]['user'];
        
        $succes = false;
        
        foreach ($user_ids as $user_id)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(InternshipOrganizerMentorRelUser :: PROPERTY_MENTOR_ID, $mentor_id);
            $conditions[] = new EqualityCondition(InternshipOrganizerMentorRelUser :: PROPERTY_USER_ID, $user_id);
            $condition = new AndCondition($conditions);
            $mentor_rel_users = InternshipOrganizerDataManager :: get_instance()->retrieve_mentor_rel_users($condition);
            if ($mentor_rel_users->next_result())
            {
                continue;
            }
            else
            {
                $mentor_rel_user = new InternshipOrganizerMentorRelUser();
                $mentor_rel_user->set_mentor_id($mentor_id);
                $mentor_rel_user->set_user_id($user_id);
                $succes = $mentor_rel_user->create();
                if ($succes)
                {
                    //                        Event :: trigger('create', 'agreement_rel_user', array('target_agreement_id' => $agreement->get_id(), 'action_user_id' => $this->user->get_id()));
                }
            }
        
        }
        
        return $succes;
    }
}

?>