<?php
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'user_type.class.php';

class InternshipOrganizerOrganisationSubscribeUsersForm extends FormValidator
{
    
    const APPLICATION_NAME = 'internship_organizer';
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    
    private $parent;
    private $organisation;
    private $user;

    function InternshipOrganizerOrganisationSubscribeUsersForm($organisation, $action, $user)
    {
        parent :: __construct('create_organisation', 'post', $action);
        
        $this->organisation = $organisation;
        $this->user = $user;
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $organisation = $this->organisation;
        $parent = $this->parent;
        
        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        $attributes['options'] = array('load_elements' => false);
        
        $this->add_receivers(self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET, Translation :: get('PublishFor'), $attributes);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->addElement('category');
        $this->addElement('html', '<br />');
        $defaults[self :: APPLICATION_NAME . '_opt_forever'] = 1;
        $defaults[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] = 0;
        $this->setDefaults($defaults);
    
    }

    function create_organisation_rel_users()
    {
        $organisation_id = $this->organisation->get_id();
        
        $values = $this->exportValues();
        
        $succes = false;
        
        if ($values[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] == 0)
        {
            //all users of the system will be subscribed if not allready subscribed
            $users = UserDataManager :: get_instance()->retrieve_users();
            
            while ($user = $users->next_result())
            {
                $user_id = $user->get_id();
                $conditions = array();
                $conditions[] = new EqualityCondition(InternshipOrganizerOrganisationRelUser :: PROPERTY_USER_ID, $user_id);
                $conditions[] = new EqualityCondition(InternshipOrganizerOrganisationRelUser :: PROPERTY_ORGANISATION_ID, $organisation_id);
                $condition = new AndCondition($conditions);
                $organisation_rel_users = InternshipOrganizerDataManager :: get_instance()->retrieve_organisation_rel_users($condition);
                if ($organisation_rel_users->next_result())
                {
                    continue;
                }
                else
                {
                    $organisation_rel_user = new InternshipOrganizerOrganisationRelUser();
                    $organisation_rel_user->set_user_id($user_id);
                    $organisation_rel_user->set_organisation_id($organisation_id);
                    $succes = $organisation_rel_user->create();
                    if ($succes)
                    {
                        //                        Event :: trigger('create', 'organisation_rel_user', array('target_organisation_id' => $organisation->get_id(), 'action_user_id' => $this->user->get_id()));
                    }
                }
            
            }
        }
        else
        {
            
            $user_ids = $values[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET . '_elements']['user'];
            $group_ids = $values[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET . '_elements']['group'];
            
            $target_users = array();
            
            if (count($user_ids))
            {
                $target_users = array_merge($target_users, $user_ids);
            
            }
            
            if (count($group_ids))
            {
                foreach ($group_ids as $group_id)
                {
                    
                    $gdm = GroupDataManager :: get_instance();
                    foreach ($target_groups as $group_id)
                    {
                        
                        $group = $gdm->retrieve_group($group_id);
                        $target_users = array_merge($target_users, $group->get_users(true, true));
                    }
                }
            
            }
            
            $target_users = array_unique($target_users);
            
            if (count($target_users))
            {
                foreach ($target_users as $user_id)
                {
                    $conditions = array();
                    $conditions[] = new EqualityCondition(InternshipOrganizerOrganisationRelUser :: PROPERTY_USER_ID, $user_id);
                    $conditions[] = new EqualityCondition(InternshipOrganizerOrganisationRelUser :: PROPERTY_ORGANISATION_ID, $organisation_id);
                    $condition = new AndCondition($conditions);
                    $organisation_rel_users = InternshipOrganizerDataManager :: get_instance()->retrieve_organisation_rel_users($condition);
                    if ($organisation_rel_users->next_result())
                    {
                        continue;
                    }
                    else
                    {
                        $organisation_rel_user = new InternshipOrganizerOrganisationRelUser();
                        $organisation_rel_user->set_user_id($user_id);
                        $organisation_rel_user->set_organisation_id($organisation_id);
                        $succes = $organisation_rel_user->create();
                        if ($succes)
                        {
                            //                   Event :: trigger('create', 'organisation_rel_user', array('target_organisation_id' => $organisation->get_id(), 'action_user_id' => $this->user->get_id()));
                        }
                    }
                }
            }
        
        }
        
        return $succes;
    }

}

?>