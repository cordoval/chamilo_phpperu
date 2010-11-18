<?php
namespace common\extensions\external_repository_manager\implementation\matterhorn;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\FormValidator;

class MatterhornExternalRepositoryManagerUploadForm extends FormValidator
{

    function MatterhornExternalRepositoryManagerUploadForm($action)
    {
        parent :: __construct('matterhorn_upload', 'post', $action);

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