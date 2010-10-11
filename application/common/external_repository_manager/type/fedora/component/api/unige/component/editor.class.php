<?php

require_once dirname(__FILE__) . '/../forms/fedora_unige_edit_form.class.php';

class FedoraUnigeEditorComponent extends FedoraExternalRepositoryManagerEditorComponent{

	function create_form($object_external_id){
		$result = new FedoraUnigeEditForm($this, $_GET, array('edit' => $object_external_id));
		return $result;
	}

}







?>