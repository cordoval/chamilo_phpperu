<?php

class InternshipOrganizerAgreementSubscribeUserForm extends FormValidator
{
    
    const APPLICATION_NAME = 'internship_organizer';
    const PARAM_TARGET = 'users';
  
    private $agreement;
    private $user_type;

    function InternshipOrganizerAgreementSubscribeUserForm($agreement, $action, $user_type)
    {
        parent :: __construct('create_agreement_rel_user', 'post', $action);
        
        $this->agreement = $agreement;
        $this->user_type = $user_type;
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $agreement = $this->agreement;
                    
        $url = Path :: get(WEB_PATH) . 'application/lib/internship_organizer/xml_feeds/xml_period_rel_user_feed.php?period_id=' . $this->agreement->get_period_id().'&user_type='.$this->user_type;
               
        $locale = array();
        $locale['Display'] = Translation :: get('ChooseCoaches');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $elem = $this->addElement('element_finder', self :: PARAM_TARGET, InternshipOrganizerUserType :: get_user_type_name($this->user_type), $url, $locale, array(), array('load_elements' => true));
        $defaults = array();
        $elem->setDefaults($defaults);
        $elem->setDefaultCollapsed(false);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    
    }

    function create_agreement_rel_user()
    {
        $agreement_id = $this->agreement->get_id();
        
        $values = $this->exportValues();
        $user_ids = $values[self :: PARAM_TARGET]['user'];
       
        $succes = false;
        
        foreach ($user_ids as $user_id)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_ID, $user_id);
            $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_AGREEMENT_ID, $agreement_id);
            $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_TYPE, $this->user_type);
            $condition = new AndCondition($conditions);
            $agreement_rel_users = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement_rel_users($condition);
            if ($agreement_rel_users->next_result())
            {
                continue;
            }
            else
            {
                $agreement_rel_user = new InternshipOrganizerAgreementRelUser();
                $agreement_rel_user->set_user_id($user_id);
                $agreement_rel_user->set_agreement_id($agreement_id);
                $agreement_rel_user->set_user_type($this->user_type);
                $succes = $agreement_rel_user->create();
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