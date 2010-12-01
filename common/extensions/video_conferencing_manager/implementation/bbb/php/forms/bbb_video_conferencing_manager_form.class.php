<?php
namespace common\extensions\video_conferencing_manager\implementation\bbb;

use common\libraries\FormValidator;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\StringUtilities;

use common\extensions\video_conferencing_manager\VideoConferencingObjectDisplay;
/**
 * $Id: bbb_video_conferencing_manager_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package
 */

class BbbVideoConferencingManagerForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    const PREVIEW = 'preview';
    const FILE = 'file';
    
    private $application;
    private $form_type;
    private $video_conferencing_object;

    function __construct($form_type, $action, $application)
    {
        parent :: __construct(Utilities :: get_classname_from_object($this, true), 'post', $action);
        
        $this->application = $application;
        
        $this->form_type = $form_type;
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    public function set_video_conferencing_object(BbbVideoConferencingObject $video_conferencing_object)
    {
        $this->video_conferencing_object = $video_conferencing_object;
        
        $defaults[BbbVideoConferencingObject :: PROPERTY_ID] = $video_conferencing_object->get_id();
        $defaults[BbbVideoConferencingObject :: PROPERTY_TITLE] = $video_conferencing_object->get_title();
        $defaults[BbbVideoConferencingObject :: PROPERTY_MODERATOR_PW] = $video_conferencing_object->get_moderator_pw();
        $defaults[BbbVideoConferencingObject :: PROPERTY_ATTENDEE_PW] = $video_conferencing_object->get_attendee_pw();
        $defaults[BbbVideoConferencingObject :: PROPERTY_WELCOME] = $video_conferencing_object->get_welcome();
        $defaults[BbbVideoConferencingObject :: PROPERTY_LOGOUT_URL] = $video_conferencing_object->get_logout_url();
        $defaults[BbbVideoConferencingObject :: PROPERTY_MAX_PARTICIPANTS] = $video_conferencing_object->get_max_participants();
        
//        $display = VideoConferencingObjectDisplay :: factory($video_conferencing_object);
//        $defaults[self :: PREVIEW] = $display->get_preview();
        
        parent :: setDefaults($defaults);
    }

    function build_basic_form()
    {
        $this->addElement('text', BbbVideoConferencingObject :: PROPERTY_TITLE, Translation :: get('Title', null, Utilities :: COMMON_LIBRARIES), array('size' => '50'));
        $this->addRule(BbbVideoConferencingObject :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $this->addElement('password', BbbVideoConferencingObject :: PROPERTY_MODERATOR_PW, Translation :: get('ModeratorPassword'), array('size' => 40, 'autocomplete' => 'off'));
        $this->addElement('password', BbbVideoConferencingObject :: PROPERTY_MODERATOR_PW . '_confirm', Translation :: get('ModeratorPasswordConfirmation'), array('size' => 40, 'autocomplete' => 'off'));
        $this->addRule(array(BbbVideoConferencingObject :: PROPERTY_MODERATOR_PW, BbbVideoConferencingObject :: PROPERTY_MODERATOR_PW . '_confirm'), Translation :: get('PassTwo'), 'compare');
        
        $this->addElement('password', BbbVideoConferencingObject :: PROPERTY_ATTENDEE_PW, Translation :: get('AttendeePassword'), array('size' => 40, 'autocomplete' => 'off'));
        $this->addElement('password', BbbVideoConferencingObject :: PROPERTY_ATTENDEE_PW . '_confirm', Translation :: get('AttendeePasswordConfirmation'), array('size' => 40, 'autocomplete' => 'off'));
        $this->addRule(array(BbbVideoConferencingObject :: PROPERTY_ATTENDEE_PW, BbbVideoConferencingObject :: PROPERTY_ATTENDEE_PW . '_confirm'), Translation :: get('PassTwo'), 'compare');
        
        $this->addElement('textarea', BbbVideoConferencingObject :: PROPERTY_WELCOME, Translation :: get('WelcomeMessage'));
        $this->addElement('text', BbbVideoConferencingObject :: PROPERTY_LOGOUT_URL, Translation :: get('LogoutUrl'), array('size' => '50'));
        $this->addElement('text', BbbVideoConferencingObject :: PROPERTY_MAX_PARTICIPANTS, Translation :: get('MaxParticipants'), array('size' => '3'));
        $this->addRule(BbbVideoConferencingObject :: PROPERTY_MAX_PARTICIPANTS, Translation :: get('ThisFieldMustBeNumeric', null, Utilities :: COMMON_LIBRARIES), 'numeric', null, 'server');
    }

    function build_editing_form()
    {
        $this->addElement('static', self :: PREVIEW);
        
        $this->build_basic_form();
        
        $this->addElement('hidden', BbbVideoConferencingObject :: PROPERTY_ID);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function create_meeting()
    {
    	$values = $this->exportValues();
        $this->video_conferencing_object->set_title($values[BbbVideoConferencingObject :: PROPERTY_TITLE]);
        $this->video_conferencing_object->set_moderator_pw($values[BbbVideoConferencingObject :: PROPERTY_MODERATOR_PW]);
        $this->video_conferencing_object->set_attendee_pw($values[BbbVideoConferencingObject :: PROPERTY_ATTENDEE_PW]);
        $this->video_conferencing_object->set_welcome($values[BbbVideoConferencingObject :: PROPERTY_WELCOME]);
        $this->video_conferencing_object->set_logout_url($values[BbbVideoConferencingObject :: PROPERTY_LOGOUT_URL]);
        $this->video_conferencing_object->set_max_participants($values[BbbVideoConferencingObject :: PROPERTY_MAX_PARTICIPANTS]);
        
    	return $this->application->get_video_conferencing_connector()->create_video_conferencing_object($this->video_conferencing_object);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
?>