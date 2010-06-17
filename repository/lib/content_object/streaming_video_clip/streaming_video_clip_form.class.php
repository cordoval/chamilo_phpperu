<?php

/**
 * Description of StreamingVideoClipForm class
 *
 * @author jevdheyd
 */

class StreamingVideoClipForm extends ContentObjectForm {

    function build_creation_form()
    {
        $link = PATH :: get_launcher_application_path(true) . 'index.php?' . Application::PARAM_APPLICATION . '=' . StreamingMediaLauncher::APPLICATION_NAME .'&'. StreamingMediaManager::PARAM_TYPE . '=' . 'mediamosa';
        
        parent :: build_creation_form();

        $this->addElement('hidden', StreamingVideoClip :: PROPERTY_SERVER_ID);
        $this->addElement('hidden', StreamingVideoClip :: PROPERTY_ASSET_ID);

        $this->addElement('static', null, null, '<a class="button normal_button upload_button" onclick="javascript:openPopup(\''. $link .'\');"> '. Translation :: get('BrowseStreamingVideo') .'</a>');
    }

    function create_content_object()
    {
        xdebug_break();
        $object = new StreamingVideoClip();

        $object->set_server_id($this->exportValue(StreamingVideoClip :: PROPERTY_SERVER_ID));
        $object->set_asset_id($this->exportValue(StreamingVideoClip :: PROPERTY_ASSET_ID));

        $this->set_content_object($object);

        return parent :: create_content_object();
    }
}
?>