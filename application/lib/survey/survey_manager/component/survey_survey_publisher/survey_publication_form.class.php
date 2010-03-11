<?php
/**
 * $Id: survey_publication_form.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component.survey_survey_publisher
 */
class SurveyPublicationForm extends FormValidator
{

    function SurveyPublicationForm($parent, $survey, $url = '')
    {
        parent :: __construct('survey', 'post', $url);
        
        $this->addElement('html', '<h4>' . $survey->get_title() . '</h4>');
        
        $users = UserDataManager :: get_instance()->retrieve_users();
        while ($user = $users->next_result())
        {
            $usrs[$user->get_id()] = $user->get_fullname();
        }
        
        $this->addElement('text', 'email_header', Translation :: get('EmailTitle'), array('size' => 80));
        $this->addRule('email_header', Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_html_editor('email_content', Translation :: get('EmailContent'), true);
        
        $this->addElement('advmultiselect', 'course_users', Translation :: get('SelectUsers'), $usrs, 'style="width: 250px;"');
        
        if ($survey->get_anonymous())
            $this->addElement('textarea', 'additional_users', Translation :: get('AdditionalUsers'), array('cols' => 50, 'rows' => 2));
        
        $this->addElement('html', '<br />' . Translation :: get('PublishSurveySendMailInfo') . '<br /><br />');
        $this->addElement('checkbox', 'resend', Translation :: get('ResendEmail'));
        $this->addElement('html', '<br />' . Translation :: get('PublishSurveyResendMailInfo') . '<br /><br />');
        //$this->addElement('submit', 'submit', Translation :: get('SendMail'));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Publish'), array('class' => 'positive publish'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
?>