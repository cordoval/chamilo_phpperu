<?php
namespace application\weblcms\tool\survey;

use common\libraries\FormValidator;
use common\libraries\Translation;

class WeblcmsSurveyPublicationMailerForm extends FormValidator
{

    const ALL_PARTICIPANTS = 'all_participants';
    const FROM_ADDRESS = 'from_address';
    const FROM_ADDRESS_NAME = 'from_address_name';
    const REPLY_ADDRESS = 'reply_address';
    const REPLY_ADDRESS_NAME = 'reply_address_name';
    const EMAIL_HEADER = 'email_header';
    const EMAIL_CONTENT = 'email_content';

    function __construct($parent, $user, $participants, $actions)
    {
        parent :: __construct('survey_publication_mailer', 'post', $actions);

        $this->addElement('text', self :: FROM_ADDRESS_NAME, Translation :: get('SurveyFromEmailAddressName', null, 'application\\survey'), array('size' => 80, 'value' => $user->get_firstname() . ' ' . $user->get_lastname()));
        $this->addRule(self :: FROM_ADDRESS_NAME, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->addElement('text', self :: FROM_ADDRESS, Translation :: get('SurveyFromEmailAddress', null, 'application\\survey'), array('size' => 80, 'value' => $user->get_email()));
        $this->addRule(self :: FROM_ADDRESS, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->addElement('text', self :: REPLY_ADDRESS_NAME, Translation :: get('SurveyReplyEmailAddressName', null, 'application\\survey'), array('size' => 80, 'value' => $user->get_firstname() . ' ' . $user->get_lastname()));
        $this->addRule(self :: REPLY_ADDRESS_NAME, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->addElement('text', self :: REPLY_ADDRESS, Translation :: get('SurveyReplyEmailAddress', null, 'application\\survey'), array('size' => 80, 'value' => $user->get_email()));
        $this->addRule(self :: REPLY_ADDRESS, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->addElement('text', self :: EMAIL_HEADER, Translation :: get('SurveyEmailTitle', null, 'application\\survey'), array('size' => 80));
        $this->addRule(self :: EMAIL_HEADER, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->add_html_editor(self :: EMAIL_CONTENT, Translation :: get('SurveyEmailContent', null, 'application\\survey'), true);

        $this->add_warning_message('attention', Translation :: get('SurveyMailAttention'), Translation :: get('SurveyAttentionSendMailInfo', null, 'application\\survey'), false);

        $this->addElement('checkbox', WeblcmsSurveyParticipantTracker :: STATUS_NOTSTARTED, Translation :: get('SurveyNotStarted', null, 'application\\survey'), ' ' . $participants[WeblcmsSurveyParticipantTracker :: STATUS_NOTSTARTED] . ' ' . Translation :: get('Participants'));
        $this->addElement('checkbox', WeblcmsSurveyParticipantTracker :: STATUS_STARTED, Translation :: get('SurveyStarted', null, 'application\\survey'), ' ' . $participants[WeblcmsSurveyParticipantTracker :: STATUS_STARTED] . ' ' . Translation :: get('Participants'));
        $this->addElement('checkbox', WeblcmsSurveyParticipantTracker :: STATUS_FINISHED, Translation :: get('SurveyFinished', null, 'application\\survey'), ' ' . $participants[WeblcmsSurveyParticipantTracker :: STATUS_FINISHED] . ' ' . Translation :: get('Participants'));

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('SendMail'), array('class' => 'positive publish'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

    }
}
?>