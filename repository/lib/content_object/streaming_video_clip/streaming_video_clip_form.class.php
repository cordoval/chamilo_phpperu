<?php

/**
 * Description of StreamingVideoClipForm class
 *
 * @author jevdheyd
 */

require_once Path :: get_application_path() . 'common/external_repository_manager/type/mediamosa/mediamosa_external_repository_connector.class.php';

class StreamingVideoClipForm extends ContentObjectForm
{

    function streaming_video_clip_form_elements()
    {
        //$link = PATH :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=' . ExternalRepositoryLauncher :: APPLICATION_NAME . '&' . ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '=' . 'mediamosa';
        
        //$this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        //$this->addElement('hidden', StreamingVideoClip :: PROPERTY_SERVER_ID);
        //$this->addElement('hidden', StreamingVideoClip :: PROPERTY_ASSET_ID);
        //$this->addElement('hidden', StreamingVideoClip :: PROPERTY_PUBLISHER);
        //$this->addElement('hidden', StreamingVideoClip :: PROPERTY_CREATOR);
        //$this->addElement('static', null, null, '<a class="button normal_button upload_button" onclick="javascript:openPopup(\'' . $link . '\');"> ' . Translation :: get('BrowseStreamingVideo') . '</a>');
        //$this->addElement('category');
    
    }

    function build_creation_form()
    {
        parent :: build_creation_form();
        
        $this->streaming_video_clip_form_elements();
    }

    function build_editing_form()
    {
        parent :: build_editing_form();
        
        $this->streaming_video_clip_form_elements();
    }
}
?>