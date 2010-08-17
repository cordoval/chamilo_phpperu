<?php

class Dokeos185TextFieldParser
{
	const TYPE_IMAGE = 'image';
	const TYPE_FLASH = 'flash';
	const TYPE_AUDIO = 'audio';
	
	function Dokeos185TextFieldParser()
	{
		
	}
	
	function factory($type = self :: TYPE_IMAGE)
	{
		$location = dirname(__FILE__) . '/text_field_parser/dokeos185_' . $type . '_text_field_parser.class.php';
		if(!file_exists($location))
		{
			throw new Exception(Translation :: get('CanNotLoadDokeos185TextFieldParser', array('TYPE' => $type)));
		}
		
		require_once($location);
		
		$class = 'Dokeos185' . Utilities :: underscores_to_camelcase($type) . 'TextFieldParser';
		return new $class();
	}
	
	static function parse($field, $types = array(self :: TYPE_IMAGE))
	{
		
	}
}

?>