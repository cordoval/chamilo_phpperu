<?php

require_once dirname(__FILE__) . '/../../../../forms/fedora_confirm_form.class.php';

class FedoraUnigeConfirmForm extends FedoraConfirmForm{

	function get_licences($key=false){
		$connector = $this->get_connector();
		$licences = $connector->retrieve_licenses();
		$lang = Translation::get_language();
		$result = array();
		foreach($licences as $lkey=>$licence){
			$text = isset($licence[$lang]) ? $licence[$lang] : $licence['english'];
			$text = '<a href="'.$lkey.'">'. $text . '</a>';
			$result[$lkey] = $text;
		}
		if($key!==false){
			return isset($result[$key]) ? $result[$key] : '';
		}else{
			return $result;
		}
	}

	function get_access_rights($key=false){
		$connector = $this->get_connector();
		$result = $connector->retrieve_rights();
		if($key!==false){
			$result = isset($result[$key]) ? $result[$key] : '';
		}
		return $result;
	}

	function get_edit_rights($key=false){
		$connector = $this->get_connector();
		$result = $connector->retrieve_rights();
		if($key!==false){
			$result = isset($result[$key]) ? $result[$key] : '';
		}
		return $result;
	}

	function get_collections($key=false){
		$result = array();
		$result['LOR:49'] = 'Unige';
		if($key!==false){
			return isset($result[$key]) ? $result[$key] : '';
		}else{
			return $result;
		}
	}

}
















?>