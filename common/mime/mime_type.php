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
	$extentions = array();
	include dirname(__FILE__) . '/extentions.php';
	return $result = $extentions;
}

function get_ext_to_mimetype(){
	static $result = false;
	if($result){
		return $result;
	}
	$mimetypes = array();
	include dirname(__FILE__) . '/mime_types.php';
	return $result = $mimetypes;
}