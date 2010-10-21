<?php namespace survey;

require_once dirname(__FILE__) . '/../survey_manager.class.php';

require_once dirname(__FILE__) . '/../../forms/survey_publication_mailer_form.class.php';
require_once dirname(__FILE__) . '/../../survey_publication_mail.class.php';
require_once Path :: get_application_path() . 'lib/survey/trackers/survey_participant_mail_tracker.class.php';

class SurveyManagerMailerComponent extends SurveyManager
{
    private $invitees;
    private $reporting_users;
    private $not_started;
    private $started;
    private $finished;
    private $mail_send = true;
    private $publication_id;
    private $survey_id;

    function run()
    {
        $this->publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        
        if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_INVITE, $this->publication_id, SurveyRights :: TYPE_PUBLICATION, $user_id))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->invitees = SurveyRights :: get_allowed_users(SurveyRights :: RIGHT_PARTICIPATE, $this->publication_id, SurveyRights :: TYPE_PUBLICATION);
        $invitee_count = count(array_unique($this->invitees));
        
        $this->reporting_users = SurveyRights :: get_allowed_users(SurveyRights :: RIGHT_REPORTING, $this->publication_id, SurveyRights :: TYPE_PUBLICATION);
        $reporting_users_count = count(array_unique($this->reporting_users));
        
        $this->not_started = array();
        
        $this->started = array();
        $this->finished = array();
        
        $survey_publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($this->publication_id);
        $this->survey_id = $survey_publication->get_content_object_id();
        $survey = RepositoryDataManager :: get_instance()->retrieve_content_object($this->survey_id);
        
        $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->publication_id);
        
        $trackers = Tracker :: get_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
        
        while ($tracker = $trackers->next_result())
        {
            $this->started[] = $tracker->get_user_id();
            if ($tracker->get_status() == SurveyParticipantTracker :: STATUS_FINISHED)
            {
                $this->finished[] = $tracker->get_user_id();
            }
        
        }
        $started_count = count(array_unique($this->started));
        
        $this->not_started = array_diff($this->invitees, $this->started);
        
        $not_started_count = count(array_unique($this->not_started));
        
        $finished_count = count(array_unique($this->finished));
        
        $users = array();
        $users[SurveyRights :: PARTICIPATE_RIGHT_NAME] = $invitee_count;
        $users[SurveyRights :: REPORTING_RIGHT_NAME] = $reporting_users_count;
        $users[SurveyParticipantTracker :: STATUS_STARTED] = $started_count;
        $users[SurveyParticipantTracker :: STATUS_NOTSTARTED] = $not_started_count;
        $users[SurveyParticipantTracker :: STATUS_FINISHED] = $finished_count;
        
        $form = new SurveyPublicationMailerForm($this, $this->get_user(), $users, $this->get_url(array(self :: PARAM_PUBLICATION_ID => $this->publication_id)));
        
        if ($form->validate())
        {
            $values = $form->exportValues();
            $this->parse_values($values);
        }
        else
        {
            $this->display_header($trail);
            echo $this->get_survey_html($survey);
            echo $form->toHtml();
            $this->display_footer();
        }
    
    }

    function get_survey_html($survey)
    {
        $html = array();
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_image_path('survey') . 'survey_22.png);">';
        $html[] = '<div class="title">' . Translation :: get('MailToUsersForSurvey') . '  ' . ' </div>';
        $html[] = $survey->get_title() . '<br/>';
        $html[] = '</div>';
        return implode("\n", $html);
    
    }

    function parse_values($values)
    {
        
        $users = array();
        $mail_user_ids = array();
        $dm = UserDataManager :: get_instance();
        
        $not_started = $values[SurveyParticipantTracker :: STATUS_NOTSTARTED];
        if ($not_started == 1)
        {
            $mail_user_ids = array_merge($mail_user_ids, $this->not_started);
        }
        
        $started = $values[SurveyParticipantTracker :: STATUS_STARTED];
        
        if ($started == 1)
        {
            $mail_user_ids = array_merge($mail_user_ids, $this->started);
        }
        
        $finished = $values[SurveyParticipantTracker :: STATUS_FINISHED];
        
        if ($finished == 1)
        {
            $mail_user_ids = array_merge($mail_user_ids, $this->finished);
        }
        
        $invitees = $values[SurveyRights :: PARTICIPATE_RIGHT_NAME];
        
        if ($invitees == 1)
        {
            
            $mail_user_ids = array_merge($mail_user_ids, $this->invitees);
        }
        
        $reporting = $values[SurveyRights :: REPORTING_RIGHT_NAME];
        $reporting_mail_user_ids = array();
        if ($reporting == 1)
        {
            $reporting_mail_user_ids = $this->reporting_users;
        }
        
        $mail_user_ids = array_unique($mail_user_ids);
        
        if ((count($mail_user_ids) + count($reporting_mail_user_ids)) == 0)
        {
            $this->redirect(Translation :: get('NoSurveyMailsSend'), false, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
        }
        else
        {
            $email_header = $values[SurveyPublicationMailerForm :: EMAIL_HEADER];
            $email_content = $values[SurveyPublicationMailerForm :: EMAIL_CONTENT];
            $email_from_address = $values[SurveyPublicationMailerForm :: FROM_ADDRESS];
            $email_reply_address = $values[SurveyPublicationMailerForm :: REPLY_ADDRESS];
            $email_from_address_name = $values[SurveyPublicationMailerForm :: FROM_ADDRESS_NAME];
            $email_reply_address_name = $values[SurveyPublicationMailerForm :: REPLY_ADDRESS_NAME];
            
            $email = new SurveyPublicationMail();
            $email->set_mail_haeder($email_header);
            $email->set_mail_content($email_content);
            $email->set_sender_user_id($this->get_user_id());
            $email->set_from_address($email_from_address);
            $email->set_from_address_name($email_from_address_name);
            $email->set_reply_address($email_reply_address);
            $email->set_reply_address_name($email_reply_address_name);
            $email->create();
            
            foreach ($mail_user_ids as $user_id)
            {
                
                $user = $dm->retrieve_user($user_id);
                $to_email = $user->get_email();
                $this->send_mail($user_id, $to_email, $email, true);
            
            }
            
            foreach ($reporting_mail_user_ids as $user_id)
            {
                $user = $dm->retrieve_user($user_id);
                $to_email = $user->get_email();
                $this->send_mail($user_id, $to_email, $email);
            }
            
            if ($this->mail_send == false)
            {
                $this->redirect(Translation :: get('NotAllMailsSend'), false, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
            }
            else
            {
                $this->redirect(Translation :: get('AllMailsSend'), false, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
            }
        
        }
    
    }

    function send_mail($user_id, $to_email, $email, $participate = false)
    {
        
        $parameters = array();
        
        if (! $participate)
        {
            $parameters[self :: PARAM_ACTION] = self :: ACTION_BROWSE;
        }
        else
        {
            $parameters[self :: PARAM_ACTION] = self :: ACTION_TAKE;
            $parameters[self :: PARAM_SURVEY_ID] = $this->survey_id;
            $parameters[self :: PARAM_PUBLICATION_ID] = $this->publication_id;
            $parameters[self :: PARAM_INVITEE_ID] = $user_id;
        }
        
        $url = Path :: get(WEB_PATH) . $this->get_link($parameters);
        
        $fullbody = array();
        $fullbody[] = $email->get_mail_content();
        $fullbody[] = '<br/><br/>';
        $fullbody[] = '<p id="link">';
        $fullbody[] = '<a href=' . $url . '>' . Translation :: get('ClickToTakeSurvey') . '</a>';
        $fullbody[] = '<br/><br/>' . Translation :: get('OrCopyAndPasteThisText') . ':';
        $fullbody[] = '<br/><a href=' . $url . '>' . $url . '</a>';
        $fullbody[] = '</p>';
        
        $arg = array();
        $args[SurveyParticipantMailTracker :: PROPERTY_USER_ID] = $user_id;
        $args[SurveyParticipantMailTracker :: PROPERTY_SURVEY_PUBLICATION_MAIL_ID] = $email->get_id();
        
        $from = array();
        $from[Mail :: NAME] = $email->get_from_address_name();
        ;
        $from[Mail :: EMAIL] = $email->get_from_address();
        
        $mail = Mail :: factory($email->get_mail_header(), implode("\n", $fullbody), $to_email, $from);
        $reply = array();
        $reply[Mail :: NAME] = $email->get_reply_address_name();
        $reply[Mail :: EMAIL] = $email->get_reply_address();
        $mail->set_reply($reply);
        
        //         Check whether it was sent successfully
        if ($mail->send() === FALSE)
        {
            $this->mail_send = false;
            $args[SurveyParticipantMailTracker :: PROPERTY_STATUS] = SurveyParticipantMailTracker :: STATUS_MAIL_NOT_SEND;
        }
        else
        {
            $args[SurveyParticipantMailTracker :: PROPERTY_STATUS] = SurveyParticipantMailTracker :: STATUS_MAIL_SEND;
        }
        
        $args[SurveyParticipantMailTracker :: PROPERTY_SURVEY_PUBLICATION_ID] = $this->publication_id;
        $tracker = Event :: trigger(SurveyParticipantMailTracker :: REGISTER_PARTICIPATION_MAIL_EVENT, SurveyManager :: APPLICATION_NAME, $args);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurveys')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID);
    }

}
?>