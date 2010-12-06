<?php 
namespace repository\content_object\survey;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ResourceManager;
use common\libraries\Path;
use user\UserDataManager;
use group\GroupDataManager;

use common\libraries\EqualityCondition;
use common\libraries\AndCondition;

class SurveyContextSubscribeGroupForm extends FormValidator
{
    
    const APPLICATION_NAME = 'survey_context_manager';
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    
    private $context;
    private $user;

    function __construct($context, $action, $user)
    {
        parent :: __construct('create_context', 'post', $action);
        
        $this->context = $context;
        $this->user = $user;
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $context = $this->context;
        
        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'group/php/xml_feeds/xml_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith', null, Utilities::COMMON_LIBRARIES);
        $locale['Searching'] = Translation :: get('Searching', null, Utilities::COMMON_LIBRARIES);
        $locale['NoResults'] = Translation :: get('NoResults', null, Utilities::COMMON_LIBRARIES);
        $locale['Error'] = Translation :: get('Error', null, Utilities::COMMON_LIBRARIES);
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        $attributes['options'] = array('load_elements' => false);
        
        $this->add_receivers(self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET, Translation :: get('PublishFor', null, Utilities::COMMON_LIBRARIES), $attributes);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('AddContextGroups'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities::COMMON_LIBRARIES), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->addElement('category');
        $this->addElement('html', '<br />');
        $defaults[self :: APPLICATION_NAME . '_opt_forever'] = 1;
        $defaults[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] = 0;
        $this->setDefaults($defaults);
    
    }

    function create_context_rel_users()
    {
        $context_id = $this->context->get_id();
        
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
                $conditions[] = new EqualityCondition(SurveyContextRelUser :: PROPERTY_USER_ID, $user_id);
                $conditions[] = new EqualityCondition(SurveyContextRelUser :: PROPERTY_CONTEXT_ID, $context_id);
                $condition = new AndCondition($conditions);
                $context_rel_users = SurveyContextDataManager :: get_instance()->retrieve_survey_context_rel_users($condition);
                if ($context_rel_users->next_result())
                {
                    continue;
                }
                else
                {
                    $context_rel_user = new SurveyContextRelUser();
                    $context_rel_user->set_user_id($user_id);
                    $context_rel_user->set_context_id($context_id);
                    $succes = $context_rel_user->create();
                    if ($succes)
                    {
                        //                        Event :: trigger('create', 'context_rel_user', array('target_context_id' => $context->get_id(), 'action_user_id' => $this->user->get_id()));
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
                    $conditions = array();
                    $conditions[] = new EqualityCondition(SurveyContextRelGroup :: PROPERTY_GROUP_ID, $group_id);
                    $conditions[] = new EqualityCondition(SurveyContextRelGroup :: PROPERTY_CONTEXT_ID, $context_id);
                    $condition = new AndCondition($conditions);
                    $context_rel_groups = SurveyContextDataManager :: get_instance()->retrieve_survey_context_rel_groups($condition);
                    if ($context_rel_groups->next_result())
                    {
                        continue;
                    }
                    else
                    {
                        $context_rel_group = new SurveyContextRelGroup();
                        $context_rel_group->set_group_id($group_id);
                        $context_rel_group->set_created(time());
                        $context_rel_group->set_context_id($context_id);
                        $succes = $context_rel_group->create();
                        if ($succes)
                        {
                            
                            $group = GroupDataManager :: get_instance()->retrieve_group($group_id);
                            $user_ids = $group->get_users(true, true);
                            foreach ($user_ids as $user_id)
                            {
                                
                                $conditions = array();
                                $conditions[] = new EqualityCondition(SurveyContextRelUser :: PROPERTY_USER_ID, $user_id);
                                $conditions[] = new EqualityCondition(SurveyContextRelUser :: PROPERTY_CONTEXT_ID, $context_id);
                                $condition = new AndCondition($conditions);
                                $context_rel_users = SurveyContextDataManager :: get_instance()->retrieve_survey_context_rel_users($condition);
                                if ($context_rel_users->next_result())
                                {
                                    continue;
                                }
                                else
                                {
                                    $context_rel_user = new SurveyContextRelUser();
                                    $context_rel_user->set_user_id($user_id);
                                    $context_rel_user->set_context_id($context_id);
                                    $succes = $context_rel_user->create();
                                    if ($succes)
                                    {
                                        //                   Event :: trigger('create', 'context_rel_user', array('target_context_id' => $context->get_id(), 'action_user_id' => $this->user->get_id()));
                                    }
                                }
                                
                            //                        Event :: trigger('create', 'context_rel_group', array('target_context_id' => $context->get_id(), 'action_user_id' => $this->user->get_id()));
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