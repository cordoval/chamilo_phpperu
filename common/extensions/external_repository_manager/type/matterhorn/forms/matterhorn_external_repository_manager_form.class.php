<?php
/**
 * 
 * $Id: matterhorn_external_repository_manager_form.class.php 
 * @package
 */

class MatterhornExternalRepositoryManagerForm extends FormValidator
{

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
//    const RESULT_SUCCESS = 'GroupUpdated';
//    const RESULT_ERROR = 'GroupUpdateFailed';

    const VIDEO_TITLE = 'title';
    const VIDEO_DESCRIPTION = 'description';

    private $application;
//    private $video_entry;
    private $form_type;
    private $external_repository_object;

    function MatterhornExternalRepositoryManagerForm($form_type, $action, $application)
    {
        parent :: __construct('matterhorn_upload', 'post', $action);

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

    public function set_external_repository_object($object)
    {
        $this->external_repository_object = $object;
    
        $this->addElement('hidden', MatterhornExternalRepositoryObject :: PROPERTY_ID);
        $defaults[MatterhornExternalRepositoryObject :: PROPERTY_TITLE] = $object->get_title();
        $defaults[MatterhornExternalRepositoryObject :: PROPERTY_DESCRIPTION] = $object->get_description();
        $defaults[MatterhornExternalRepositoryObject :: PROPERTY_DURATION] = $object->get_duration();
        $defaults[MatterhornExternalRepositoryObject :: PROPERTY_CONTRIBUTORS] = $object->get_contributors();
        $defaults[MatterhornExternalRepositoryObject :: PROPERTY_SERIES] = $object->get_series();
        $defaults[MatterhornExternalRepositoryObject :: PROPERTY_OWNER_ID] = $object->get_owner_id();
        $defaults[MatterhornExternalRepositoryObject :: PROPERTY_CREATED] = $object->get_created();
        $defaults[MatterhornExternalRepositoryObject :: PROPERTY_SUBJECTS] = $object->get_subjects();
        $defaults[MatterhornExternalRepositoryObject :: PROPERTY_LICENSE] = $object->get_license();
        $defaults[MatterhornExternalRepositoryObject :: PROPERTY_TYPE] = $object->get_type();
        $defaults[MatterhornExternalRepositoryObject :: PROPERTY_MODIFIED] = $object->get_modified();
        $defaults[MatterhornExternalRepositoryObject :: PROPERTY_TRACKS] = $object->get_tracks();
        $defaults[MatterhornExternalRepositoryObject :: PROPERTY_ATTACHMENTS] = $object->get_attachments();

        parent :: setDefaults($defaults);
    }

    function build_basic_form()
    {
        $this->addElement('text', MatterhornExternalRepositoryObject :: PROPERTY_TITLE, Translation :: get('Title'), array("size" => "50"));
        $this->addRule(MatterhornExternalRepositoryObject :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

//        $this->addElement('select', MatterhornExternalRepositoryObject :: PROPERTY_CATEGORY, Translation :: get('Category'), $this->get_youtube_categories());
//
//        $this->addElement('textarea', YoutubeExternalRepositoryObject :: PROPERTY_TAGS, Translation :: get('Tags'), array("rows" => "2", "cols" => "80"));
//        $this->addRule(YoutubeExternalRepositoryObject :: PROPERTY_TAGS, Translation :: get('ThisFieldIsRequired'), 'required');
		$this->addElement('text', MatterhornExternalRepositoryObject :: PROPERTY_CONTRIBUTORS, Translation :: get('Contributors'), array("size" => "50"));
		$this->addElement('text', MatterhornExternalRepositoryObject :: PROPERTY_SERIES, Translation :: get('Series'), array("size" => "50"));
        $this->addElement('textarea', MatterhornExternalRepositoryObject :: PROPERTY_DESCRIPTION, Translation :: get('Description'), array("rows" => "7", "cols" => "80"));
        $this->addElement('text', MatterhornExternalRepositoryObject :: PROPERTY_OWNER_ID, Translation :: get('Creator'), array("size" => "50"));
        $this->addElement('text', MatterhornExternalRepositoryObject :: PROPERTY_TYPE, Translation :: get('Type'), array("size" => "50"));
        $this->addElement('text', MatterhornExternalRepositoryObject :: PROPERTY_LICENSE, Translation :: get('License'), array("size" => "50"));
        $this->addElement('text', MatterhornExternalRepositoryObject :: PROPERTY_SUBJECTS, Translation :: get('Subjects'), array("size" => "50"));
        
    }

    function build_editing_form()
    {
        $this->build_basic_form();

        $this->addElement('hidden', MatterhornExternalRepositoryObject :: PROPERTY_ID);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Edit'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
		$this->addElement('file', 'track', Translation :: get('File'));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_video_entry()
    {
        $matterhorn = $this->application->get_external_repository_connector();
        $values = $this->exportValues();

        return $matterhorn->update_matterhorn_video($values);

    }
    
	function upload_video()
    {
        if (StringUtilities :: has_value(($_FILES['track']['name'])))
        {
            return $this->application->get_external_repository_connector()->create_external_repository_object($this->exportValues(), $_FILES['track']['tmp_name']);
        }
        else
        {
            return false;
        }
    }

//    function get_upload_token()
//    {
//        $values = $this->exportValues();
//
//        $connector = $this->application->get_external_repository_connector();
//        return $connector->get_upload_token($values);
//    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
//    function setDefaults($defaults = array ())
//    {
////        $defaults[MatterhornExternalRepositoryObject::PROPERTY_ID] = $this->video_entry->get_id();
////    	$defaults[MatterhornExternalRepositoryObject :: PROPERTY_TITLE] = $this->video_entry->get_title();
////        $defaults[MatterhornExternalRepositoryObject :: PROPERTY_DESCRIPTION] = $this->video_entry->get_description();
//        parent :: setDefaults($defaults);
//    }

//    function get_entry_video()
//    {
//        return $this->entry_video;
//    }
}
?>