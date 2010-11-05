<?php
namespace application\metadata;

use common\libraries\FormValidator;
use common\libraries\ResourceManager;
use common\libraries\Path;
use common\libraries\Translation;

class UploadForm extends FormValidator
{
    function UploadForm($url)
    {
        
        parent :: __construct('upload_form', 'post', $url);
        $this->build_upload_form();
    }

    function build_upload_form()
    {
        $this->addElement('file', Translation :: get('File'));
        $this->addElement('style_submit_button', 'submit', Translation :: get('Upload'), array('class' => 'positive update'));
    }
}
?>
