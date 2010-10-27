<?php
namespace common\extensions\feedback_manager;

use admin\AdminDataManager;
use common\libraries\FormValidator;
use common\libraries\Translation;
use repository\content_object\feedback\Feedback;
use admin\FeedbackPublication;

/**
 * Description of feeback_text_formclass
 *
 * @author pieter
 */


class FeedbackManagerForm extends FormValidator
{
    private $adm;

    const PROPERTY_TEXT = 'text';

    function FeedbackManagerForm($action)
    {
        parent :: __construct('feedback_form', 'post', $action);
        $this->build_text_form();

        $this->adm = AdminDataManager :: get_instance();
    }

    function build_text_form()
    {
        $this->add_html_editor(self :: PROPERTY_TEXT, 'comment', 'required');
        $this->addRule(self :: PROPERTY_TEXT, Translation :: get('ThisFieldIsRequired'), 'required');
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function create_feedback($owner, $publication_id, $complex_wrapper_id, $application)
    {
        $feedback = new Feedback();

        $values = $this->exportValues(self :: PROPERTY_TEXT);
        $feedback->set_description($values[self :: PROPERTY_TEXT]);
        $feedback->set_title(Translation :: get('QuickFeedback'));
        $feedback->set_owner_id($owner);

        $feedback->set_parent_id(0);
        $feedback->set_icon(Feedback :: ICON_INFORMATIVE);
        $feedback->create();
        $fb = new FeedbackPublication();
        $fb->set_application($application);
        $fb->set_cid($complex_wrapper_id);
        $fid = $feedback->get_id();
        $fb->set_fid($fid);
        $fb->set_pid($publication_id);
        $fb->set_creation_date(time());
        $fb->set_modification_date(time());

        return $fb->create();
    }

}
?>