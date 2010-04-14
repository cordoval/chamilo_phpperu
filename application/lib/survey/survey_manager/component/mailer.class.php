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

    	if (! SurveyRights :: is_allowed(SurveyRights :: MAIL_RIGHT, 'publication_browser', 'sts_component'))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }

    	$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
//        $trail->add(new Breadcrumb($this->get_mail_survey_participant_url(), Translation :: get('MailParticipants')));

        $ids = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            if(count($ids) == 1){
            	$survey_publication = $this->retrieve_survey_publication($ids[0]);
            	$trail->add(new Breadcrumb($this->get_mail_survey_participant_url($survey_publication), Translation :: get('MailParticipants')));
            }

            $surveys = array();

            $this->not_started = array();
            $this->started = array();
            $this->finished = array();

            foreach ($ids as $id)
            {
                $survey_publication = $this->retrieve_survey_publication($id);
                $survey_id = $survey_publication->get_content_object();
                $surveys[] = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_id);

            }

            $condition = new InCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $ids);

            $dummy = new SurveyParticipantTracker();
            $not_started_condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_STATUS, SurveyParticipantTracker :: STATUS_NOTSTARTED);
            $notstarted_trackers = $dummy->retrieve_tracker_items(new AndCondition(array($condition, $not_started_condition)));

            foreach ($notstarted_trackers as $tracker)
            {

                $this->not_started[$tracker->get_survey_publication_id()][] = $tracker->get_user_id();

            }

            $not_started_users = array();
            foreach ($this->not_started as $users)
            {
                $not_started_users = array_merge($not_started_users, $users);
            }
            $not_started_count = count(array_unique($not_started_users));

            $started_condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_STATUS, SurveyParticipantTracker :: STATUS_STARTED);
            $started_trackers = $dummy->retrieve_tracker_items(new AndCondition(array($condition, $started_condition)));

            foreach ($started_trackers as $tracker)
            {
                $this->started[$tracker->get_survey_publication_id()][] = $tracker->get_user_id();
            }

            $started_users = array();
            foreach ($this->started as $users)
            {
                $started_users = array_merge($started_users, $users);
            }
            $started_count = count(array_unique($started_users));

            $finished_condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_STATUS, SurveyParticipantTracker :: STATUS_FINISHED);
            $finished_trackers = $dummy->retrieve_tracker_items(new AndCondition(array($condition, $finished_condition)));
            //			$finished = array ();
            foreach ($finished_trackers as $tracker)
            {
                $this->finished[$tracker->get_survey_publication_id()][] = $tracker->get_user_id();
            }
            $finished_users = array();
            foreach ($this->started as $users)
            {
                $finished_users = array_merge($finished_users, $users);
            }
            $finished_count = count(array_unique($finished_users));

            $participants = array();
            $participants[SurveyParticipantTracker :: STATUS_STARTED] = $started_count;
            $participants[SurveyParticipantTracker :: STATUS_NOTSTARTED] = $not_started_count;
            $participants[SurveyParticipantTracker :: STATUS_FINISHED] = $finished_count;

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
        else
        {
            $this->redirect(Translation :: get('NoParticipantSelected'), false, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
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
        if ($not_started == 1)
        {
            foreach ($this->not_started as $survey_id => $user_ids)
            {
                foreach ($user_ids as $user_id)
                {
                    $mail_users[$user_id][] = $survey_id;
                }
            }
        }

        $started = $values[SurveyParticipantTracker :: STATUS_STARTED];

        if ($started == 1)
        {
            foreach ($this->started as $survey_id => $user_ids)
            {
                foreach ($user_ids as $user_id)
                {
                    $mail_users[$user_id][] = $survey_id;
                }
            }
        }

        $finished = $values[SurveyParticipantTracker :: STATUS_FINISHED];

        if ($finished == 1)
        {
            foreach ($this->finished as $survey_id => $user_ids)
            {
                foreach ($user_ids as $user_id)
                {
                    $mail_users[$user_id][] = $survey_id;
                }
            }
        }

        if (count(array_values($mail_users)) == 0)
        {
            $this->redirect(Translation :: get('NoSurveyParticipantMailsSend'), false, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
        }
        else
        {
            $email_header = $values[SurveyPublicationMailerForm :: EMAIL_HEADER];
            $email_content = $values[SurveyPublicationMailerForm :: EMAIL_CONTENT];
            $email_from_address = $values[SurveyPublicationMailerForm :: FROM_ADDRESS];
            $email_reply_address = $values[SurveyPublicationMailerForm :: REPLY_ADDRESS];

            $email = new SurveyPublicationMail();
            $email->set_mail_haeder($email_header);
            $email->set_mail_content($email_content);
            $email->set_sender_user_id($this->get_user_id());
            $email->set_from_address($email_from_address);
            $email->set_reply_address($email_reply_address);

            $email->create();

            foreach ($mail_users as $user_id => $survey_ids)
            {

                $user = $dm->retrieve_user($user_id);
                $to_email = $user->get_email();
                $this->send_mail($user_id, $to_email, $email, $survey_ids);

            }
            if ($this->mail_send == false)
            {
                $this->redirect(Translation :: get('NotAllSurveyParticipantMailsSend'), false, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
            }
            else
            {
                $this->redirect(Translation :: get('AllSurveyParticipantMailsSend'), false, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
            }

        }

    }

    function send_mail($user_id, $to_email, $email, $survey_ids)
    {
        $fullbody = array();
        $parameters = array();
        if (count($survey_ids) != 1)
        {
            $parameters[SurveyManager :: PARAM_ACTION] = SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS;
        }
        else
        {
            $parameters[SurveyManager :: PARAM_ACTION] = SurveyManager :: ACTION_VIEW_SURVEY_PUBLICATION;
            $parameters[SurveyManager :: PARAM_SURVEY_PUBLICATION] = $survey_ids[0];

        }
        $url = Path :: get(WEB_PATH) . $this->get_link($parameters);

        $fullbody[] = $this->get_mail_header();
        $fullbody[] = $email->get_mail_content();
        $fullbody[] = '<br/><br/><a href=' . $url . '>' . Translation :: get('ClickToTakeSurvey') . '</a>';
        $fullbody[] = '<br/><br/>' . Translation :: get('OrCopyAndPasteThisText') . ':';
        $fullbody[] = '<br/><a href=' . $url . '>' . $url . '</a>';
        $fullbody[] = '<br/>';
        $fullbody[] = $this->get_mail_footer();

//        echo implode('', $fullbody);
//        exit;

        //$email->set_mail_content($fullbody);
        //$email->update();
        //echo $email . $email_header . $fullbody . '<br/>';


        //		exit;
        $arg = array();
        $args[SurveyParticipantMailTracker :: PROPERTY_USER_ID] = $user_id;
        $args[SurveyParticipantMailTracker :: PROPERTY_SURVEY_PUBLICATION_MAIL_ID] = $email->get_id();

        $from = array();
        $from[Mail :: NAME] = '';
        $from[Mail :: EMAIL] = $email->get_from_address();

        $mail = Mail :: factory($email->get_mail_header(), implode("\n" ,$fullbody), $to_email, $from);
        $reply = array();
        $reply[Mail :: NAME] = '';
        $reply[Mail :: EMAIL] = $email->get_reply_address();
        $mail->set_reply($reply);
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

        foreach ($survey_ids as $survey_id)
        {

            $args[SurveyParticipantMailTracker :: PROPERTY_SURVEY_PUBLICATION_ID] = $survey_id;

            $tracker = Events :: trigger_event('survey_participation_mail', 'survey', $args);
        }

    }

    function get_mail_header()
    {
        $html = array();

        $header = new Header();
        $header->add_default_headers();
        $header->set_page_title(PlatformSetting :: get('site_name'));
        $header->add_html_header('<style type="text/css">body {background-color:white; width: 100%;}</style>');

        $html[] = $header->toHtml();
        $html[] = '<body>';
        $html[] = '<div id="header1" style="width: 100%;">';
        $html[] = '<div class="banner"><span class="logo">&nbsp;</span></div>';
        $html[] = '</div>';
        $html[] = '<div id="trailbox" style="height: 10px;">&nbsp;';
        $html[] = '</div>';
        $html[] = '<div class="content_object" style="padding: 10px; margin: 10px;">';

        return implode("\n", $html);
    }

    function get_mail_footer()
    {
       $html = array();
       $html[] = '</div>';
       $html[] = '<div class="clear">';
       $html[] = '</body>';
       $html[] = '</html>';

       return implode("\n", $html);
    }
}
?>