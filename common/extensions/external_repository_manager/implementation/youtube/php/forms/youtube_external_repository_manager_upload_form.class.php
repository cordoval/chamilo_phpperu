<?php
namespace common\extensions\external_repository_manager\implementation\youtube;

use common\libraries\Translation;
use common\libraries\FormValidator;
use common\libraries\Utilities;

class YoutubeExternalRepositoryManagerUploadForm extends FormValidator
{
    private $token;

    function __construct($action, $token)
    {
        parent :: __construct('youtube_upload', 'post', $action);

        $this->token = $token;
        $this->build_upload_form();
    }

    function build_upload_form()
    {
        $this->addElement('hidden', 'token', $this->token);
    	$this->addElement('file', 'file', sprintf(Translation :: get('FileName'), '2Gb'));

    	$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Upload', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
?>