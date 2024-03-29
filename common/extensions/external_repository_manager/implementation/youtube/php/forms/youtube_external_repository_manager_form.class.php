<?php
namespace common\extensions\external_repository_manager\implementation\youtube;

use common\extensions\external_repository_manager\ExternalRepositoryObject;
use common\libraries\Translation;
use common\libraries\FormValidator;
use common\libraries\Utilities;

/**
 * $Id: youtube_external_repository_manager_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package
 */

class YoutubeExternalRepositoryManagerForm extends FormValidator
{

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'GroupUpdated';
    const RESULT_ERROR = 'GroupUpdateFailed';

    const VIDEO_TITLE = 'title';
    const VIDEO_CATEGORY = 'category';
    const VIDEO_TAGS = 'tags';
    const VIDEO_DESCRIPTION = 'description';

    private $application;
    private $video_entry;
    private $form_type;
    private $external_repository_object;

    function __construct($form_type, $action, $application)
    {
        parent :: __construct('youtube_upload', 'post', $action);

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

    public function set_external_repository_object(YoutubeExternalRepositoryObject $external_repository_object)
    {
        $this->external_repository_object = $external_repository_object;
        $this->addElement('hidden', ExternalRepositoryObject :: PROPERTY_ID);
        $defaults[YoutubeExternalRepositoryObject :: PROPERTY_TITLE] = $external_repository_object->get_title();
        $defaults[YoutubeExternalRepositoryObject :: PROPERTY_DESCRIPTION] = $external_repository_object->get_description();
        $defaults[YoutubeExternalRepositoryObject :: PROPERTY_CATEGORY] = $external_repository_object->get_category();
        $defaults[YoutubeExternalRepositoryObject :: PROPERTY_TAGS] = $this->get_tags();
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
        $this->addElement('text', YoutubeExternalRepositoryObject :: PROPERTY_TITLE, Translation :: get('Title', null, Utilities :: COMMON_LIBRARIES), array("size" => "50"));
        $this->addRule(YoutubeExternalRepositoryObject :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');

        $this->addElement('select', YoutubeExternalRepositoryObject :: PROPERTY_CATEGORY, Translation :: get('Category', null, Utilities :: COMMON_LIBRARIES), $this->get_youtube_categories());

        $this->addElement('textarea', YoutubeExternalRepositoryObject :: PROPERTY_TAGS, Translation :: get('Tags'), array("rows" => "2", "cols" => "80"));
        $this->addRule(YoutubeExternalRepositoryObject :: PROPERTY_TAGS, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');

        $this->addElement('textarea', YoutubeExternalRepositoryObject :: PROPERTY_DESCRIPTION, Translation :: get('Description', null, Utilities :: COMMON_LIBRARIES), array("rows" => "7", "cols" => "80"));
    }

    function get_youtube_categories()
    {
        $connector = YoutubeExternalRepositoryManagerConnector :: get_instance($this->application->get_external_repository());
        return $connector->retrieve_categories();
    }

    function build_editing_form()
    {
        $this->build_basic_form();

        $this->addElement('hidden', YoutubeExternalRepositoryObject :: PROPERTY_ID);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_video_entry()
    {
        $youtube = $this->application->get_external_repository_manager_connector();
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

        $connector = $this->application->get_external_repository_manager_connector();
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