<?php

require_once dirname(__FILE__) . '/../survey_manager.class.php';
require_once dirname(__FILE__) . '/../survey_manager_component.class.php';
require_once dirname(__FILE__) . '/../../forms/survey_publication_mailer_form.class.php';
require_once dirname(__FILE__) . '/../../survey_publication_mail.class.php';
require_once Path :: get_application_path() . 'lib/survey/trackers/survey_participant_mail_tracker.class.php';

class SurveyManagerMailerComponent extends SurveyManagerComponent
{
    private $not_started;
    private $started;
    private $finished;
    private $mail_send = true;

    function run()
    {
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('MailParticipants')));
        
        $ids = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        
        $failures = 0;
        
        $surveys = array();
        
        $this->not_started = array();
        $this->started = array();
        $this->finished = array();
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $surveys[] = RepositoryDataManager :: get_instance()->retrieve_content_object($id);
            
            }
            
            $condition = new InCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $ids);
            
            $dummy = new SurveyParticipantTracker();
            $not_started_condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_STATUS, SurveyParticipantTracker :: STATUS_NOTSTARTED);
            $notstarted_trackers = $dummy->retrieve_tracker_items(new AndCondition(array($condition, $not_started_condition)));
            $not_started = array();
            $not_started_surveys = array();
            foreach ($notstarted_trackers as $tracker)
            {
                if (! array_key_exists($tracker->get_survey_publication_id()))
                {
                     $not_started[$tracker->get_survey_publication_id()] = $tracker->get_user_id();
                }
            
            }
            $this->not_started = array_unique($not_started);
            
            $started_condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_STATUS, SurveyParticipantTracker :: STATUS_STARTED);
            $started_trackers = $dummy->retrieve_tracker_items(new AndCondition(array($condition, $started_condition)));
            
            $started = array();
            foreach ($started_trackers as $tracker)
            {
                $started[$tracker->get_survey_publication_id()] = $tracker->get_user_id();
            }
            $this->started = array_unique($started);
            
            $finished_condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_STATUS, SurveyParticipantTracker :: STATUS_FINISHED);
            $finished_trackers = $dummy->retrieve_tracker_items(new AndCondition(array($condition, $finished_condition)));
            $finished = array();
            foreach ($finished_trackers as $tracker)
            {
                $finished[$tracker->get_survey_publication_id()] = $tracker->get_user_id();
            }
            $this->finished = array_unique($finished);
        
        }
        
        $participants = array();
        $participants[SurveyParticipantTracker :: STATUS_STARTED] = count($this->started);
        $participants[SurveyParticipantTracker :: STATUS_NOTSTARTED] = count($this->not_started);
        $participants[SurveyParticipantTracker :: STATUS_FINISHED] = count($this->finished);
        
        $form = new SurveyPublicationMailerForm($this, $this->get_user(), $participants, $this->get_url(array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $ids)));
        
        if ($form->validate())
        {
            $values = $form->exportValues();
            $this->parse_values($values);
        }
        else
        {
            $this->display_header($trail);
            echo $this->get_survey_html($surveys);
            echo $form->toHtml();
            $this->display_footer();
        }
    }

    function get_survey_html($surveys)
    {
        $html = array();
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_image_path('survey') . 'survey_22.png);">';
        $html[] = '<div class="title">' . Translation :: get('MailToParticipantsForSurveys') . '  ' . ' </div>';
        $i = 1;
        foreach ($surveys as $survey)
        {
            $html[] = $i . ': ' . $survey->get_title() . '<br/>';
            $i ++;
        }
        $html[] = '</div>';
        return implode("\n", $html);
    
    }

    function parse_values($values)
    {
        
        $users = array();
        $mail_users = array();
        $dm = UserDataManager :: get_instance();
        
        $not_started = $values[SurveyParticipantTracker :: STATUS_NOTSTARTED];
        dump($not_started);
        if ($not_started == 1)
        {
            $users = $this->not_started;
        }
        
        $started = $values[SurveyParticipantTracker :: STATUS_STARTED];
        
        if ($started == 1)
        {
            $users = array_merge($users, $this->started);
        }
        
        $finished = $values[SurveyParticipantTracker :: STATUS_FINISHED];
        
        if ($finished == 1)
        {
            $users = array_merge($users, $this->finished);
        }
        //        dump($this->not_started);
        //        dump($users);
        //        exit;
        

        foreach ($users as $user_id)
        {
            $user = $dm->retrieve_user($user_id);
            $mail_users[$user_id] = $user->get_email();
        }
        
        if (count($mail_users) == 0)
        {
            $this->redirect(Translation :: get('NoUserMailsSend'), false, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
        }
        else
        {
            $email_header = $values[SurveyPublicationMailerForm :: EMAIL_HEADER];
            $email_content = $values[SurveyPublicationMailerForm :: EMAIL_CONTENT];
            $email_from_address = $values[SurveyPublicationMailerForm :: FROM_ADDRESS];
            
            $email = new SurveyPublicationMail();
            $email->set_mail_haeder($email_header);
            $email->set_mail_content($email_content);
            $email->set_sender_user_id($this->get_user_id());
            $email->set_from_address($email_from_address);
            
            $email->create();
            
            foreach ($mail_users as $user_id => $to_mail)
            {
                $this->send_mail($user_id, $to_email, $email);
            
            }
            if ($this->mail_send == false)
            {
                $this->redirect(Translation :: get('NoUserMailsSend'), false, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
            }
            else
            {
                $this->redirect(Translation :: get('UserMailsSend'), false, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
            }
        
        }
    
    }

    function send_mail($user_id, $to_email, $email)
    {
        $url = Path :: get(WEB_PATH) . $this->get_link(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
        $text = '<br/><br/><a href=' . $url . '>' . Translation :: get('ClickToTakeSurvey') . '</a>';
        $text .= '<br/><br/>' . Translation :: get('OrCopyAndPasteThisText') . ':';
        $text .= '<br/><a href=' . $url . '>' . $url . '</a>';
        $fullbody = $email->get_mail_content() . $text . '<br/>';
        
        //echo $email . $email_header . $fullbody . '<br/>';
        

        $arg = array();
        $args[SurveyParticipantMailTracker :: PROPERTY_USER_ID] = $user_id;
        $args[SurveyParticipantMailTracker :: PROPERTY_SURVEY_PUBLICATION_MAIL_ID] = $email->get_id();
        $args[SurveyParticipantMailTracker :: PROPERTY_SURVEY_PUBLICATION_ID] = 1;
        
        $mail = Mail :: factory($email->get_mail_header(), $fullbody, $to_email, $email->get_from_address());
        // Check whether it was sent successfully
        if ($mail->send() === FALSE)
        {
            $this->mail_send = false;
            $args[SurveyParticipantMailTracker :: PROPERTY_STATUS] = SurveyParticipantMailTracker :: STATUS_MAIL_NOT_SEND;
        }
        else
        {
            $args[SurveyParticipantMailTracker :: PROPERTY_STATUS] = SurveyParticipantMailTracker :: STATUS_MAIL_SEND;
        }
        
        $tracker = Events :: trigger_event('survey_participant_mail', 'survey', $args);
    
    }
}
?>