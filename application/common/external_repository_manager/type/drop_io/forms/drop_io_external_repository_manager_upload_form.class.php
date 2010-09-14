<?php
class DropIoExternalRepositoryManagerUploadForm extends FormValidator
{

    function DropIoExternalRepositoryManagerUploadForm($action)
    {
        parent :: __construct('drop_io_upload', 'post', $action);

        $this->build_upload_form();
    }

    function build_upload_form()
    {    	
        $this->addElement('hidden', 'token', $this->token);
    	$this->addElement('file', 'file', sprintf(Translation :: get('FileName'), '2Gb'));
    	
    	$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Upload'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
?>