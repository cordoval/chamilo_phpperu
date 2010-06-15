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

}
?>