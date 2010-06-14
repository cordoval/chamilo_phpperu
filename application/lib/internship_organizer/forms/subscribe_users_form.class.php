<?php
require_once dirname(__FILE__) . '/../user_type.class.php';

class InternshipOrganizerSubscribeUsersForm extends FormValidator
{
    
    const APPLICATION_NAME = 'internship_organizer';
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    
    private $parent;
    private $period;
    private $user;

    function InternshipOrganizerSubscribeUsersForm($period, $action, $user)
    {
        parent :: __construct('create_period', 'post', $action);
        
        $this->period = $period;
        $this->user = $user;
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $period = $this->period;
        $parent = $this->parent;
        
        $this->addElement('select', InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE, Translation :: get('InternshipOrganizerUserType'), InternshipOrganizerUserType :: get_user_type_names());
        $this->addRule(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE, Translation :: get('ThisFieldIsRequired'), 'required');
        
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

    function create_period_rel_users()
    {
        $period_id = $this->period->get_id();
        
        $values = $this->exportValues();
        $user_type = $values[InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE];
        
        $succes = false;
        
        if ($values[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] == 0)
        {
            //all users of the system will be subscribed if not allready subscribed
            $users = UserDataManager :: get_instance()->retrieve_users();
            
            while ($user = $users->next_result())
            {
                $user_id = $user->get_id();
                $conditions = array();
                $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_ID, $user_id);
                $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, $period_id);
                $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE, $user_type);
                $condition = new AndCondition($conditions);
                $period_rel_users = InternshipOrganizerDataManager :: get_instance()->retrieve_period_rel_users($condition);
                if ($period_rel_users->next_result())
                {
                    continue;
                }
                else
                {
                    $period_rel_user = new InternshipOrganizerPeriodRelUser();
                    $period_rel_user->set_user_id($user_id);
                    $period_rel_user->set_user_type($user_type);
                    $period_rel_user->set_period_id($period_id);
                    $succes = $period_rel_user->create();
                    if ($succes)
                    {
                        //                        Events :: trigger_event('create', 'period_rel_user', array('target_period_id' => $period->get_id(), 'action_user_id' => $this->user->get_id()));
                    }
                }
            
            }
        }
        else
        {

        	$user_ids = $values[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET . '_elements']['user'];
            $group_ids = $values[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET . '_elements']['group'];
          
            if (count($user_ids))
            {
                foreach ($user_ids as $user_id)
                {
                    $conditions = array();
                    $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_ID, $user_id);
                    $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, $period_id);
                    $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE, $user_type);
                    $condition = new AndCondition($conditions);
                    $period_rel_users = InternshipOrganizerDataManager :: get_instance()->retrieve_period_rel_users($condition);
                    if ($period_rel_users->next_result())
                    {
                        continue;
                    }
                    else
                    {
                        $period_rel_user = new InternshipOrganizerPeriodRelUser();
                        $period_rel_user->set_user_id($user_id);
                        $period_rel_user->set_user_type($user_type);
                        $period_rel_user->set_period_id($period_id);
                        $succes = $period_rel_user->create();
                        if ($succes)
                        {
                            //                        Events :: trigger_event('create', 'period_rel_user', array('target_period_id' => $period->get_id(), 'action_user_id' => $this->user->get_id()));
                        }
                    }
                }
            }
            if (count($group_ids))
            {
                foreach ($group_ids as $group_id)
                {
                    $conditions = array();
                    $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelGroup :: PROPERTY_GROUP_ID, $group_id);
                    $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelGroup :: PROPERTY_PERIOD_ID, $period_id);
                    $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelGroup :: PROPERTY_USER_TYPE, $user_type);
                    $condition = new AndCondition($conditions);
                    $period_rel_groups = InternshipOrganizerDataManager :: get_instance()->retrieve_period_rel_groups($condition);
                    if ($period_rel_groups->next_result())
                    {
                        continue;
                    }
                    else
                    {
                        $period_rel_group = new InternshipOrganizerPeriodRelGroup();
                        $period_rel_group->set_group_id($group_id);
                        $period_rel_group->set_user_type($user_type);
                        $period_rel_group->set_period_id($period_id);
                        $succes = $period_rel_group->create();
                        if ($succes)
                        {
                            //                        Events :: trigger_event('create', 'period_rel_user', array('target_period_id' => $period->get_id(), 'action_user_id' => $this->user->get_id()));
                        }
                    }
                }
            }
        
        }
             
        return $succes;
    }

}

?>