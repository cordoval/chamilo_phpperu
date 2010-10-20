<?php

class SurveyPublicationMailerForm extends FormValidator
{

	const ALL_PARTICIPANTS = 'all_participants';
	const FROM_ADDRESS = 'from_address';
	const FROM_ADDRESS_NAME = 'from_address_name';
	const REPLY_ADDRESS = 'reply_address';
	const REPLY_ADDRESS_NAME = 'reply_address_name';
	const EMAIL_HEADER = 'email_header';
	const EMAIL_CONTENT = 'email_content';

    function SurveyPublicationMailerForm($parent, $user,$users, $actions)
    {
        parent :: __construct('survey_publication_mailer', 'post', $actions);

        $this->addElement('text', self :: FROM_ADDRESS_NAME, Translation :: get('SurveyFromEmailAddressName'), array('size' => 80, 'value'=>$user->get_firstname().' '.$user->get_lastname()));
        $this->addRule(self :: FROM_ADDRESS_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', self :: FROM_ADDRESS, Translation :: get('SurveyFromEmailAddress'), array('size' => 80, 'value'=>$user->get_email()));
        $this->addRule(self :: FROM_ADDRESS, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', self :: REPLY_ADDRESS_NAME, Translation :: get('SurveyReplyEmailAddressName'), array('size' => 80, 'value'=>$user->get_firstname().' '.$user->get_lastname()));
        $this->addRule(self :: REPLY_ADDRESS_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', self :: REPLY_ADDRESS, Translation :: get('SurveyReplyEmailAddress'), array('size' => 80, 'value'=>$user->get_email()));
        $this->addRule(self :: REPLY_ADDRESS, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', self :: EMAIL_HEADER, Translation :: get('SurveyEmailTitle'), array('size' => 80));
        $this->addRule(self :: EMAIL_HEADER, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_html_editor(self :: EMAIL_CONTENT, Translation :: get('SurveyEmailContent'), true);

        $this->add_warning_message('attention', Translation :: get('SurveyMailAttention'), Translation :: get('SurveyAttentionSendMailInfo'), false );
		
        $this->addElement('checkbox', SurveyRights::PARTICIPATE_RIGHT_NAME, Translation :: get('Invitees'), ' '.$users[SurveyRights::PARTICIPATE_RIGHT_NAME].' '.Translation :: get('Invitees'));
        $this->addElement('checkbox', SurveyParticipantTracker :: STATUS_NOTSTARTED, Translation :: get('SurveyNotStarted'), ' '.$users[SurveyParticipantTracker :: STATUS_NOTSTARTED].' '.Translation :: get('Participants'));
        $this->addElement('checkbox', SurveyParticipantTracker :: STATUS_STARTED, Translation :: get('SurveyStarted'), ' '.$users[SurveyParticipantTracker :: STATUS_STARTED].' '.Translation :: get('Participants'));
        $this->addElement('checkbox', SurveyParticipantTracker :: STATUS_FINISHED, Translation :: get('SurveyFinished'), ' '.$users[SurveyParticipantTracker :: STATUS_FINISHED].' '.Translation :: get('Participants'));
	
        $this->addElement('checkbox', SurveyRights::REPORTING_RIGHT_NAME, Translation :: get('ReportingUsers'), ' '.$users[SurveyRights::REPORTING_RIGHT_NAME].' '.Translation :: get('ReportingUsers'));
        
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('SendMail'), array('class' => 'positive publish'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

//        InvitationManager :: get_elements($this, false);

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

    }
}
?>