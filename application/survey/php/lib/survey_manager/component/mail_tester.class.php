<?php
namespace application\survey;

use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use repository\RepositoryDataManager;
use common\libraries\EqualityCondition;
use tracking\Tracker;
use tracking\Event;
use common\libraries\Theme;
use user\UserDataManager;
use common\libraries\path;
use common\libraries\Mail;

class SurveyManagerMailTesterComponent extends SurveyManager
{
    private $invitees;
    private $reporting_users;
    private $not_started;
    private $started;
    private $finished;
    private $publication_id;
    private $survey_id;

    function run()
    {
        $this->publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        
        if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_INVITE, $this->publication_id, SurveyRights :: TYPE_PUBLICATION, $this->get_user_id()))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $form = new SurveyMailTesterForm($this, $this->get_user(), $this->get_url(array(
                self :: PARAM_PUBLICATION_ID => $this->publication_id)));
        
        if ($form->validate())
        {
            $values = $form->exportValues();
            $this->parse_values($values);
        }
        else
        {
            $this->display_header();
            echo $form->toHtml();
            $this->display_footer();
        }
    
    }

    function parse_values($values)
    {
        
        $email_header = $values[SurveyMailTesterForm :: EMAIL_HEADER];
        $email_content = $values[SurveyMailTesterForm :: EMAIL_CONTENT];
        $email_from_address = $values[SurveyMailTesterForm :: FROM_ADDRESS];
        $email_reply_address = $values[SurveyMailTesterForm :: REPLY_ADDRESS];
        $email_to_address = $values[SurveyMailTesterForm :: TO_ADDRESS];
        $email_from_address_name = $values[SurveyMailTesterForm :: FROM_ADDRESS_NAME];
        $email_reply_address_name = $values[SurveyMailTesterForm :: REPLY_ADDRESS_NAME];
        $email_to_address_name = $values[SurveyMailTesterForm :: TO_ADDRESS_NAME];
        $email_count = $values[SurveyMailTesterForm :: EMAILCOUNT];
        $email_asked_count = $email_count;
        $index = 1;
        
        $meta_logs = array();
        $initial_start = time();
        $meta_logs['start'] = date('D, d M Y H:i:s', $initial_start);
        $failed_count = 0;
        while ($email_count > 0)
        {
            $email_count --;
            
            $from = array();
            $from[Mail :: NAME] = $email_from_address_name;
            $from[Mail :: EMAIL] = $email_from_address;
            
            $mail = Mail :: factory($email_header, $email_content, $email_to_address, $from);
            
            $reply = array();
            $reply[Mail :: NAME] = $email_reply_address_name;
            $reply[Mail :: EMAIL] = $email_reply_address;
            $mail->set_reply($reply);
            
            $logs = array();
            $start = time();
            $logs['count'] = $index ++;
            $logs['start'] = date('D, d M Y H:i:s', $start);
            
            //         Check whether it was sent successfully
            if ($mail->send() === FALSE)
            {
                $mail_send = false;
                $logs['mail send'] = 'false';
                $failed_count++;
            }
            else
            {
                
                $logs['mail send'] = 'false';
            
            }
            $end = time();
            $logs['end'] = date('D, d M Y H:i:s', $end);
            $logs['time'] = $end - $start.' secs';
            dump($logs);
        }
        
        $end = time();
        $meta_logs['end'] = date('D, d M Y H:i:s', $end);
        $meta_logs['time'] = $end - $initial_start.' secs';
        $meta_logs['mails asked'] = $email_asked_count;
        $meta_logs['failed'] = $failed_count;
        $meta_logs['succeed'] = $email_asked_count-$failed_count;
        dump('Statistics');
        dump($meta_logs);
    }

}

function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
{
    $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurveys')));
}

function get_additional_parameters()
{
    return array(self :: PARAM_PUBLICATION_ID);
}
?>