<?php

function mimetype_to_ext($mime){
	$key = strtolower($mime);
	$map = get_mimetype_to_ext();
	return isset($map[$key]) ? $map[$key] : '';
}

function ext_to_mimetype($ext){
	$key = strtolower($ext);
	$map = get_ext_to_mimetype();
	return isset($map[$key]) ? $map[$key] : '';
}

function get_mimetype_to_ext(){
	static $result = false;
	if($result){
		return $result;
	}
	$result = array();
	$items = get_ext_to_mimetype();
	foreach($items as $ext=>$mime){
		$result[$mime] = $ext;
	}
	return $result;
}

function get_ext_to_mimetype(){
	static $result = false;
	if($result){
		return $result;
	}
	include dirname(__FILE__) . '/mime_types.php';
	$mimetypes;
	return $result = $mimetypes;
}