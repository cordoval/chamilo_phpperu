<?php

class SurveyPublicationMailerForm extends FormValidator
{
	
	const ALL_PARTICIPANTS = 'all_participants';	
	const FROM_ADDRESS = 'from_address';
	const EMAIL_HEADER = 'email_header';
	const EMAIL_CONTENT = 'email_content';

    function SurveyPublicationMailerForm($parent, $user,$participants, $actions)
    {
        parent :: __construct('survey_publication_mailer', 'post', $actions);
        
        $this->addElement('text', self :: FROM_ADDRESS, Translation :: get('FromEmailAddress'), array('size' => 80, 'value'=>$user->get_email()));
        $this->addElement('text', self :: EMAIL_HEADER, Translation :: get('EmailTitle'), array('size' => 80));
        $this->addRule(self :: EMAIL_HEADER, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_html_editor(self :: EMAIL_CONTENT, Translation :: get('EmailContent'), true);
        
        $this->add_warning_message('attention', Translation :: get('Attention'), Translation :: get('AttentionSendMailInfo'), false );

        $this->addElement('checkbox', SurveyParticipantTracker :: STATUS_NOTSTARTED, Translation :: get('NotStarted'), ' '.$participants[SurveyParticipantTracker :: STATUS_NOTSTARTED].' '.Translation :: get('Participants'));
        $this->addElement('checkbox', SurveyParticipantTracker :: STATUS_STARTED, Translation :: get('Started'), ' '.$participants[SurveyParticipantTracker :: STATUS_STARTED].' '.Translation :: get('Participants'));
        $this->addElement('checkbox', SurveyParticipantTracker :: STATUS_FINISHED, Translation :: get('Finished'), ' '.$participants[SurveyParticipantTracker :: STATUS_FINISHED].' '.Translation :: get('Participants'));
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('SendMail'), array('class' => 'positive publish'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
    }
}
?>