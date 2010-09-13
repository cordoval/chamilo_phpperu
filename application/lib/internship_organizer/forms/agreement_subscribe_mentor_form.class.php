<?php

class InternshipOrganizerAgreementSubscribeMentorForm extends FormValidator
{
    
    const APPLICATION_NAME = 'internship_organizer';
    const PARAM_TARGET = 'mentors';
    
    private $parent;
    private $agreement;
    private $user;

    function InternshipOrganizerAgreementSubscribeMentorForm($agreement, $action, $user)
    {
        parent :: __construct('create_agreement', 'post', $action);
        
        $this->agreement = $agreement;
        $this->user = $user;
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $agreement = $this->agreement;
        $parent = $this->parent;
        
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID, $agreement->get_id());
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_TYPE, InternshipOrganizerAgreementRelLocation :: APPROVED);
        $condition = new AndCondition($conditions);
        $locations = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement_rel_locations($condition);
        $location = $locations->next_result();
        $location_id = $location->get_location_id();
            
        $url = Path :: get(WEB_PATH) . 'application/lib/internship_organizer/xml_feeds/xml_mentor_feed.php?location_id=' . $location_id;
        
        $locale = array();
        $locale['Display'] = Translation :: get('ChooseMentors');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $elem = $this->addElement('element_finder', self :: PARAM_TARGET, Translation :: get('InternshipOrganizerMentors'), $url, $locale, array(), array('load_elements' => true));
        $defaults = array();
        $elem->setDefaults($defaults);
        $elem->setDefaultCollapsed(false);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    
    }

    function create_agreement_rel_mentor()
    {
        $agreement_id = $this->agreement->get_id();
        
        $values = $this->exportValues();
        $mentor_ids = $values[self :: PARAM_TARGET]['mentor'];
        
        $succes = false;
        
        foreach ($mentor_ids as $mentor_id)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelMentor :: PROPERTY_MENTOR_ID, $mentor_id);
            $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelMentor :: PROPERTY_AGREEMENT_ID, $agreement_id);
            $condition = new AndCondition($conditions);
            $agreement_rel_mentors = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement_rel_mentors($condition);
            if ($agreement_rel_mentors->next_result())
            {
                continue;
            }
            else
            {
                $agreement_rel_mentor = new InternshipOrganizerAgreementRelMentor();
                $agreement_rel_mentor->set_mentor_id($mentor_id);
                $agreement_rel_mentor->set_agreement_id($agreement_id);
                $succes = $agreement_rel_mentor->create();
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