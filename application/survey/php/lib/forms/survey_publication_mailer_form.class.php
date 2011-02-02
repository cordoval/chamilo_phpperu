<?php 
namespace application\survey;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\FormValidator;
use group\GroupDataManager;
use user\UserDataManager;

class SurveyPublicationMailerForm extends FormValidator
{

	const APPLICATION_NAME = 'survey';
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    const PARAM_RIGHTS = 'rights';
	
    const ALL_PARTICIPANTS = 'all_participants';
    const FROM_ADDRESS = 'from_address';
    const FROM_ADDRESS_NAME = 'from_address_name';
    const REPLY_ADDRESS = 'reply_address';
    const REPLY_ADDRESS_NAME = 'reply_address_name';
    const EMAIL_HEADER = 'email_header';
    const EMAIL_CONTENT = 'email_content';
    const USERS_NOT_SELECTED_COUNT ='users_not_selected_count';
    
    const FORM_NAME ='survey_publication_mailer';

    function __construct($parent, $user, $users, $actions)
    {
        parent :: __construct(self ::FORM_NAME, 'post', $actions);
        
       
        
        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'group/php/xml_feeds/xml_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        $attributes['options'] = array('load_elements' => false);
        
        $this->add_receivers(self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET, Translation :: get('SubscribeGroups'), $attributes);
       
//        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('AddGroups'), array('class' => 'positive update'));
//        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
//        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
       
        $defaults[self :: APPLICATION_NAME . '_opt_forever'] = 1;
        $defaults[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] = 0;
      
        
//        $this->addElement('category');
//        $this->addElement('html', '<br />');
        $this->addElement('text', self :: FROM_ADDRESS_NAME, Translation :: get('SurveyFromEmailAddressName'), array('size' => 80, 'value' => $user->get_firstname() . ' ' . $user->get_lastname()));
        $this->addRule(self :: FROM_ADDRESS_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', self :: FROM_ADDRESS, Translation :: get('SurveyFromEmailAddress'), array('size' => 80, 'value' => $user->get_email()));
        $this->addRule(self :: FROM_ADDRESS, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', self :: REPLY_ADDRESS_NAME, Translation :: get('SurveyReplyEmailAddressName'), array('size' => 80, 'value' => $user->get_firstname() . ' ' . $user->get_lastname()));
        $this->addRule(self :: REPLY_ADDRESS_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', self :: REPLY_ADDRESS, Translation :: get('SurveyReplyEmailAddress'), array('size' => 80, 'value' => $user->get_email()));
        $this->addRule(self :: REPLY_ADDRESS, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', self :: EMAIL_HEADER, Translation :: get('SurveyEmailTitle'), array('size' => 80));
        $this->addRule(self :: EMAIL_HEADER, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_html_editor(self :: EMAIL_CONTENT, Translation :: get('SurveyEmailContent'), true);
        
        $this->add_warning_message('attention', Translation :: get('SurveyMailAttention'), Translation :: get('SurveyAttentionSendMailInfo'), false);
        
//        $this->add_warning_message('attention', Translation :: get('SurveyMailUsersNotSelectedCount'), $users[self :: USERS_NOT_SELECTED_COUNT], false);
        
        
        $this->addElement('checkbox', SurveyRights :: PARTICIPATE_RIGHT_NAME, Translation :: get('Invitees'), ' ' . $users[SurveyRights :: PARTICIPATE_RIGHT_NAME] . ' ' . Translation :: get('Invitees'));
        $this->addElement('checkbox', SurveyParticipantTracker :: STATUS_NOTSTARTED, Translation :: get('SurveyNotStarted'), ' ' . $users[SurveyParticipantTracker :: STATUS_NOTSTARTED] . ' ' . Translation :: get('Participants'));
        $this->addElement('checkbox', SurveyParticipantTracker :: STATUS_STARTED, Translation :: get('SurveyStarted'), ' ' . $users[SurveyParticipantTracker :: STATUS_STARTED] . ' ' . Translation :: get('Participants'));
        $this->addElement('checkbox', SurveyParticipantTracker :: STATUS_FINISHED, Translation :: get('SurveyFinished'), ' ' . $users[SurveyParticipantTracker :: STATUS_FINISHED] . ' ' . Translation :: get('Participants'));
        
//        $this->addElement('checkbox', SurveyRights :: REPORTING_RIGHT_NAME, Translation :: get('ReportingUsers'), ' ' . $users[SurveyRights :: REPORTING_RIGHT_NAME] . ' ' . Translation :: get('ReportingUsers'));
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('SendMail'), array('class' => 'positive publish'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        //        InvitationManager :: get_elements($this, false);
        

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
     	 $this->setDefaults($defaults);
    }
    
 function get_seleted_group_user_ids()
    {
        
//        $publication_id = $this->publication->get_id();
        
        $values = $this->exportValues();
         
        $user_ids = array();
        
        if ($values[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] == 0)
        {
            //all users of the system will be checked
            $users = UserDataManager :: get_instance()->retrieve_users();
            
            while ($user = $users->next_result())
            {
                $user_ids[] = $user->get_id();
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
                }
            }
        }
        
        return $user_ids;
    }
}
?>