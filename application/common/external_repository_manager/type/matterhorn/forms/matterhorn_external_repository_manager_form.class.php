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
    const VIDEO_CATEGORY = 'category';
    const VIDEO_TAGS = 'tags';
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
    
        $this->addElement('hidden', ExternalRepositoryObject :: PROPERTY_ID);
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

        parent :: setDefaults($defaults);
    }

    public function get_tags()
    {
        $external_repository_object = $this->external_repository_object;
        $tags = $external_repository_object->get_tags();
        return implode(",", $tags);
    }

    function build_basic_form()
    {
        $this->addElement('text', YoutubeExternalRepositoryObject :: PROPERTY_TITLE, Translation :: get('Title'), array("size" => "50"));
        $this->addRule(YoutubeExternalRepositoryObject :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('select', YoutubeExternalRepositoryObject :: PROPERTY_CATEGORY, Translation :: get('Category'), $this->get_youtube_categories());

        $this->addElement('textarea', YoutubeExternalRepositoryObject :: PROPERTY_TAGS, Translation :: get('Tags'), array("rows" => "2", "cols" => "80"));
        $this->addRule(YoutubeExternalRepositoryObject :: PROPERTY_TAGS, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('textarea', YoutubeExternalRepositoryObject :: PROPERTY_DESCRIPTION, Translation :: get('Description'), array("rows" => "7", "cols" => "80"));
    }

    function get_youtube_categories()
    {
        $connector = YoutubeExternalRepositoryConnector :: get_instance($this->application->get_external_repository());
        return $connector->retrieve_categories();
    }

    function build_editing_form()
    {
        $this->build_basic_form();

        $this->addElement('hidden', YoutubeExternalRepositoryObject :: PROPERTY_ID);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Edit'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_video_entry()
    {
        $youtube = $this->application->get_external_repository_connector();
        $values = $this->exportValues();

        return $youtube->update_youtube_video($values);

    /*if ($value)
        {
            Event :: trigger('update', 'video_entry', array('video_entry_id' => $this->video_entry->getId()));
        }
        return $value;*/
    }

    function get_upload_token()
    {
        $values = $this->exportValues();

        $connector = $this->application->get_external_repository_connector();
        return $connector->get_upload_token($values);
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        //        $defaults[self :: VIDEO_TITLE] = $this->video_entry->getVideoTitle();
        //        $defaults[self :: VIDEO_CATEGORY] = $this->video_entry->getVideoCategory();
        //        $defaults[self :: VIDEO_TAGS] = $this->video_entry->getVideoTags();
        //        $defaults[self :: VIDEO_DESCRIPTION] = $this->video_entry->getVideoDescription();
        parent :: setDefaults($defaults);
    }

    function get_entry_video()
    {
        return $this->entry_video;
    }
}
?>