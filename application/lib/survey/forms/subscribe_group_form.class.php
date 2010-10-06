<?php

class SurveySubscribeGroupForm extends FormValidator
{
    
    const APPLICATION_NAME = 'survey';
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    const PARAM_RIGHTS = 'rights';
    
    private $parent;
    private $publication;
    private $user;

    function SurveySubscribeGroupForm($publication, $action, $user)
    {
        parent :: __construct('subscribe_group', 'post', $action);
        
        $this->publication = $publication;
        $this->user = $user;
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $publication = $this->publication;
        
        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'group/xml_feeds/xml_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        $attributes['options'] = array('load_elements' => false);
        
        $this->add_receivers(self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET, Translation :: get('SubscribeGroups'), $attributes);
        
        $rights = SurveyRights :: get_available_rights_for_publications();
        foreach ($rights as $right_name => $right)
        {
            $check_boxes[] = $this->createElement('checkbox', $right, $right_name, $right_name . '  ');
        }
        $this->addGroup($check_boxes, self :: PARAM_RIGHTS, Translation :: get('Rights'), '&nbsp;', true);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('AddGroups'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->addElement('category');
        $this->addElement('html', '<br />');
        $defaults[self :: APPLICATION_NAME . '_opt_forever'] = 1;
        $defaults[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] = 0;
        $this->setDefaults($defaults);
    
    }

    function create_group_rights()
    {
        
        $publication_id = $this->publication->get_id();
        
        $values = $this->exportValues();
        
        $succes = false;
        
        $location_id = SurveyRights :: get_location_id_by_identifier_from_surveys_subtree($publication_id, SurveyRights :: TYPE_PUBLICATION);
        
        if ($values[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] == 0)
        {
            //all users of the system will be subscribed if not allready subscribed
            $users = UserDataManager :: get_instance()->retrieve_users();
            
            while ($user = $users->next_result())
            {
                $user_id = $user->get_id();
                
                foreach ($values[self :: PARAM_RIGHTS] as $right => $value)
                {
                    if ($value == 1)
                    {
                        $succes = RightsUtilities :: set_user_right_location_value($right, $user_id, $location_id, 1);
                    }
                }
            }
        }
        else
        {
            $group_ids = $values[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET . '_elements']['group'];
            
            if (count($group_ids))
            {
                foreach ($group_ids as $group_id)
                {
                    $group_user_ids = array();
                    foreach ($group_ids as $group_id)
                    {
                        
                        $group = GroupDataManager :: get_instance()->retrieve_group($group_id);
                        $ids = $group->get_users(true, true);
                        $group_user_ids = array_merge($group_user_ids, $ids);
                    
                    }
                    $user_ids = array_unique($group_user_ids);
                    
                    foreach ($user_ids as $user_id)
                    {
                        foreach ($values[self :: PARAM_RIGHTS] as $right => $value)
                        {
                            if ($value == 1)
                            {
                                $succes = RightsUtilities :: set_user_right_location_value($right, $user_id, $location_id, 1);
                            }
                        }
                    }
                }
            }
        }
        
        return $succes;
    }

}

?>