<?php
namespace application\metadata;

use common\libraries\FormValidator;
use common\libraries\ResourceManager;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Utilities;

class UploadForm extends FormValidator
{
    function UploadForm($url)
    {
        
        parent :: __construct('upload_form', 'post', $url);
        $this->build_upload_form();
    }

    function build_upload_form()
    {
        $this->addElement('file', Translation :: get('File', null, Utilities :: COMMON_LIBRARIES));
        $this->addElement('style_submit_button', 'submit', Translation :: get('Upload', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive update'));
    }
}
?>
