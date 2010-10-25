<?php

class InternshipOrganizerMentorSubscribeLocationForm extends FormValidator
{
    
    const APPLICATION_NAME = 'internship_organizer';
    const PARAM_TARGET = 'locations';
    
    private $mentor;
    private $user;

    function InternshipOrganizerMentorSubscribeLocationForm($mentor, $action, $user)
    {
        parent :: __construct('create_mentor_rel_location', 'post', $action);
        
        $this->mentor = $mentor;
        $this->user = $user;
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $mentor = $this->mentor;
        
        $url = Path :: get(WEB_PATH) . 'application/internship_organizer/php/xml_feeds/xml_location_feed.php?organisation_id=' . $mentor->get_organisation_id();
        
        $locale = array();
        $locale['Display'] = Translation :: get('ChooseLocations');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $elem = $this->addElement('element_finder', self :: PARAM_TARGET, Translation :: get('InternshipOrganizerLocations'), $url, $locale, array(), array('load_elements' => true));
        $defaults = array();
        $elem->setDefaults($defaults);
        $elem->setDefaultCollapsed(false);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function create_mentor_rel_location()
    {
        $mentor_id = $this->mentor->get_id();
        
        $values = $this->exportValues();
        
        $location_ids = $values[self :: PARAM_TARGET]['location'];
        
        $succes = false;
        
        foreach ($location_ids as $location_id)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(InternshipOrganizerMentorRelLocation :: PROPERTY_MENTOR_ID, $mentor_id);
            $conditions[] = new EqualityCondition(InternshipOrganizerMentorRelLocation :: PROPERTY_LOCATION_ID, $location_id);
            $condition = new AndCondition($conditions);
            $mentor_rel_locations = InternshipOrganizerDataManager :: get_instance()->retrieve_mentor_rel_locations($condition);
            if ($mentor_rel_locations->next_result())
            {
                continue;
            }
            else
            {
                $mentor_rel_location = new InternshipOrganizerMentorRelLocation();
                $mentor_rel_location->set_mentor_id($mentor_id);
                $mentor_rel_location->set_location_id($location_id);
                $succes = $mentor_rel_location->create();
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