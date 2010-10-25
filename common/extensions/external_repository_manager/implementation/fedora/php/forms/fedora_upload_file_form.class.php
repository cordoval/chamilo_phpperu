<?php

/**
 * Fedora form for uploading a file.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraUploadFileForm extends FormValidator{

	function __construct($application, $parameters, $data = false){
		parent::__construct(__CLASS__, 'post', Redirect::get_url($parameters));

        $this->build_upload_form();
    }

	function validate(){
		return isset($_FILES['file']) && !empty($_FILES['file']['tmp_name']);
	}

    function build_upload_form(){
    	$this->addElement('file', 'file', Translation::get('FileName'));
		$this->addRule('file', Translation::get('Required'), 'required');

    	$buttons[] = $this->createElement('style_submit_button', 'submit', Translation::get('Upload'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation::get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}


?>