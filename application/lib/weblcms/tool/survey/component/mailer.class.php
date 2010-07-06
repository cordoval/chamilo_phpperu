<?php
require_once dirname(__FILE__) . '/../survey_publication_mailer_form.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/trackers/weblcms_survey_participant_mail_tracker.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/trackers/weblcms_survey_participant_tracker.class.php';

class SurveyToolMailerComponent extends SurveyTool
{
	private $not_started;
    private $started;
    private $finished;
    private $mail_send = true;

    function run()
    {        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
        //        $trail->add(new Breadcrumb($this->get_mail_survey_participant_url(), Translation :: get('MailParticipants')));
        

        $ids = Request :: get(SurveyTool :: PARAM_PUBLICATION_ID);
               
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            if (count($ids) == 1)
            {
                $survey_publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($ids[0]);
                $trail->add(new Breadcrumb($this->get_mail_survey_participant_url($survey_publication), Translation :: get('MailParticipants')));
            }
            
            $surveys = array();
            
            $this->not_started = array();
            $this->started = array();
            $this->finished = array();
            
            foreach ($ids as $id)
            {
                $survey_publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($id);
                $surveys[] = $survey_publication->get_content_object();            
            }
            
            $condition = new InCondition(WeblcmsSurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $ids);
            
            $dummy = new WeblcmsSurveyParticipantTracker();
            $not_started_condition = new EqualityCondition(WeblcmsSurveyParticipantTracker :: PROPERTY_STATUS, WeblcmsSurveyParticipantTracker :: STATUS_NOTSTARTED);
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
            
            $started_condition = new EqualityCondition(WeblcmsSurveyParticipantTracker :: PROPERTY_STATUS, WeblcmsSurveyParticipantTracker :: STATUS_STARTED);
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
            
            $finished_condition = new EqualityCondition(WeblcmsSurveyParticipantTracker :: PROPERTY_STATUS, WeblcmsSurveyParticipantTracker :: STATUS_FINISHED);
            $finished_trackers = $dummy->retrieve_tracker_items(new AndCondition(array($condition, $finished_condition)));
            //			$finished = array ();
            foreach ($finished_trackers as $tracker)
            {
                $this->finished[$tracker->get_survey_publication_id()][] = $tracker->get_user_id();
            }
            $finished_users = array();
            foreach ($this->finished as $users)
            {
                $finished_users = array_merge($finished_users, $users);
            }
            $finished_count = count(array_unique($finished_users));
            
            $participants = array();
            $participants[WeblcmsSurveyParticipantTracker :: STATUS_STARTED] = $started_count;
            $participants[WeblcmsSurveyParticipantTracker :: STATUS_NOTSTARTED] = $not_started_count;
            $participants[WeblcmsSurveyParticipantTracker :: STATUS_FINISHED] = $finished_count;
            
            $form = new WeblcmsSurveyPublicationMailerForm($this, $this->get_user(), $participants, $this->get_url(array(SurveyTool :: PARAM_PUBLICATION_ID => $ids)));
            
            if ($form->validate())
            {
                $values = $form->exportValues();
                $this->parse_values($values);
            }
            else
            {
                $this->display_header();
                echo $this->get_survey_html($surveys);
                echo $form->toHtml();
                $this->display_footer();
            }
        
        }
        else
        {
            $this->redirect(Translation :: get('NoParticipantSelected'), false, array(SurveyTool :: PARAM_ACTION => SurveyTool :: ACTION_BROWSE));
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
        
        $not_started = $values[WeblcmsSurveyParticipantTracker :: STATUS_NOTSTARTED];
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
        
        $started = $values[WeblcmsSurveyParticipantTracker :: STATUS_STARTED];
        
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
        
        $finished = $values[WeblcmsSurveyParticipantTracker :: STATUS_FINISHED];
        
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
            $this->redirect(Translation :: get('NoSurveyParticipantMailsSend'), false, array(SurveyTool :: PARAM_ACTION => SurveyTool :: ACTION_VIEW));
        }
        else
        {
            $email_header = $values[WeblcmsSurveyPublicationMailerForm :: EMAIL_HEADER];
            $email_content = $values[WeblcmsSurveyPublicationMailerForm :: EMAIL_CONTENT];
            $email_from_address = $values[WeblcmsSurveyPublicationMailerForm :: FROM_ADDRESS];
            $email_reply_address = $values[WeblcmsSurveyPublicationMailerForm :: REPLY_ADDRESS];
            $email_from_address_name = $values[WeblcmsSurveyPublicationMailerForm :: FROM_ADDRESS_NAME];
            $email_reply_address_name = $values[WeblcmsSurveyPublicationMailerForm :: REPLY_ADDRESS_NAME];
                   
            $email = new SurveyPublicationMail();
            $email->set_mail_haeder($email_header);
            $email->set_mail_content($email_content);
            $email->set_sender_user_id($this->get_user_id());
            $email->set_from_address($email_from_address);
            $email->set_from_address_name($email_from_address_name);
            $email->set_reply_address($email_reply_address);
            $email->set_reply_address_name($email_reply_address_name);
//            $email->create();
            
            foreach ($mail_users as $user_id => $survey_ids)
            {
                
                $user = $dm->retrieve_user($user_id);
                $to_email = $user->get_email();
                $this->send_mail($user_id, $to_email, $email, $survey_ids);
            
            }
            if ($this->mail_send == false)
            {
                $this->redirect(Translation :: get('NotAllSurveyParticipantMailsSend'), false, array(SurveyTool :: PARAM_ACTION => SurveyTool :: ACTION_VIEW));
            }
            else
            {
                $this->redirect(Translation :: get('AllSurveyParticipantMailsSend'), false, array(SurveyTool :: PARAM_ACTION => SurveyTool :: ACTION_VIEW));
            }
        
        }
    
    }

    function send_mail($user_id, $to_email, $email, $survey_ids)
    {
    	
    	$fullbody = array();
        $parameters = array();
        
        $unique_surveys = array_unique($survey_ids);
        
        if (count($unique_surveys) != 1)
        {
            $parameters[SurveyTool :: PARAM_ACTION] = SurveyTool :: ACTION_VIEW;
        }
        else
        {
            $parameters[SurveyTool :: PARAM_ACTION] = SurveyTool :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT;
            $parameters[SurveyTool :: PARAM_PUBLICATION_ID] = $unique_surveys[0];
        }
        
        $url = Path :: get(WEB_PATH) . $this->get_link($parameters);
        
        //        $fullbody[] = $this->get_mail_header($email);
        $fullbody[] = $email->get_mail_content();
        $fullbody[] = '<br/><br/>';
        $fullbody[] = '<p id="link">';
        $fullbody[] = '<a href=' . $url . '>' . Translation :: get('ClickToTakeSurvey') . '</a>';
        $fullbody[] = '<br/><br/>' . Translation :: get('OrCopyAndPasteThisText') . ':';
        $fullbody[] = '<br/><a href=' . $url . '>' . $url . '</a>';
        $fullbody[] = '</p>';
        //        $fullbody[] = $this->get_mail_footer();
        

        //                echo implode('', $fullbody);
        //                exit;
        

        //$email->set_mail_content($fullbody);
        //$email->update();
        //echo $email . $email_header . $fullbody . '<br/>';
        

        //		exit;
        $arg = array();
        $args[WeblcmsSurveyParticipantMailTracker :: PROPERTY_USER_ID] = $user_id;
        $args[WeblcmsSurveyParticipantMailTracker :: PROPERTY_SURVEY_PUBLICATION_MAIL_ID] = $email->get_id();
        
        $from = array();
        $from[Mail :: NAME] = $email->get_from_address_name();;
        $from[Mail :: EMAIL] = $email->get_from_address();
        
        $mail = Mail :: factory($email->get_mail_header(), implode("\n", $fullbody), $to_email, $from);
        $reply = array();
        $reply[Mail :: NAME] = $email->get_reply_address_name();
        $reply[Mail :: EMAIL] = $email->get_reply_address();
        $mail->set_reply($reply);
        
        // Check whether it was sent successfully
        if ($mail->send() === FALSE)
        {
            $this->mail_send = false;
            $args[WeblcmsSurveyParticipantMailTracker :: PROPERTY_STATUS] = WeblcmsSurveyParticipantMailTracker :: STATUS_MAIL_NOT_SEND;
        }
        else
        {
            $args[WeblcmsSurveyParticipantMailTracker :: PROPERTY_STATUS] = WeblcmsSurveyParticipantMailTracker :: STATUS_MAIL_SEND;
        }
        
        foreach ($unique_surveys as $survey_id)
        {
            
            $args[WeblcmsSurveyParticipantMailTracker :: PROPERTY_SURVEY_PUBLICATION_ID] = $survey_id;
            
            $tracker = Event :: trigger('weblcms_survey_participation_mail', 'weblcms', $args);
        }
    
    }

    function get_mail_header($email)
    {
        $html = array();
        
        //        $header = new Header();
        //        $header->add_css_file_header(Theme :: get_theme_path() . 'css/common_mail.css');
        //        $header->set_page_title(PlatformSetting :: get('site_name'));
        //        $html[] = $header->toHtml();
        

        $html[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $html[] = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
        $html[] = '<head>';
        //$html[] = '<style type="text/css" media="screen,projection"> /*<![CDATA[*/ @import "'. Theme :: get_theme_path() . 'css/common_mail.css' .'"; /*]]>*/ </style>';
        //        $html[] = '<link rel="stylesheet" href="'. Theme :: get_theme_path() . 'css/common_mail.css" type="text/css" media="screen" />';
        //        $html[] = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        $html[] = '<title>' . PlatformSetting :: get('site_name') . '</title>';
        $html[] = '</head>';
        
        $html[] = '<body>';
        //        $html[] = '<body bottommargin="0" leftmargin="0" marginheight="0" marginwidth="0" rightmargin="0" topmargin="0">';
        //        $html[] = '<table id="main" cellpadding="0" cellspacing="0">';
        //        $html[] = '<tr class="header">';
        //        $html[] = '<td>';
        //        $html[] = '<img src="'. Theme :: get_common_image_path() .'logo_header.png" />';
        //        $html[] = '</td>';
        //        $html[] = '</tr>';
        //        $html[] = '<tr class="divider">';
        //        $html[] = '<td>';
        //        $html[] = '<a href="'. PlatformSetting :: get('institution_url') .'">' . PlatformSetting :: get('institution') . '</a>';
        //        $html[] = '&nbsp;|&nbsp;';
        //        $html[] = PlatformSetting :: get('site_name');
        //        $html[] = '&nbsp;|&nbsp;';
        //        $html[] = $email->get_mail_header();
        //        $html[] = '</td>';
        //        $html[] = '</tr>';
        //        $html[] = '<tr class="content">';
        //        $html[] = '<td>';
        

        return implode("\n", $html);
    }

    function get_mail_footer()
    {
        $html = array();
        
        //        $html[] = '</td>';
        //        $html[] = '</tr>';
        //        $html[] = '<tr class="footer">';
        //        $html[] = '<td>';
        //        $html[] = '<a href="http://www.chamilo.org"><img src="'. Theme :: get_common_image_path() .'logo_footer.png" /></a>';
        //        $html[] = '</td>';
        //        $html[] = '</tr>';
        //        $html[] = '<tr>';
        //        $html[] = '</table>';
        $html[] = '</body>';
        $html[] = '</html>';
        
        return implode("\n", $html);
    }
}
?>