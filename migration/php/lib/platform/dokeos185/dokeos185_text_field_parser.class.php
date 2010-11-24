<?php
namespace migration;

use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * Generic parser framework to parse several included objects from a given dokeos 185 field
 * @author svenvanpoucke
 *
 */
class Dokeos185TextFieldParser
{
	const TYPE_IMAGE = 'image';
	const TYPE_FLASH = 'flash';
	const TYPE_AUDIO = 'audio';
	
	/**
	 * List with included objects so we don't need to parse the text field again for included objects
	 * @var int[]
	 */
	private $included_objects;
	
	function __construct()
	{
		$this->included_objects = array();	
	}
	
	function factory($type = self :: TYPE_IMAGE)
	{
		$location = dirname(__FILE__) . '/text_field_parser/dokeos185_' . $type . '_text_field_parser.class.php';
		if(!file_exists($location))
		{
			throw new Exception(Translation :: get('CanNotLoadDokeos185TextFieldParser', array('TYPE' => $type)));
		}
		
		require_once($location);
		
		$class = __NAMESPACE__ . '\\' . 'Dokeos185' . Utilities :: underscores_to_camelcase($type) . 'TextFieldParser';
		return new $class();
	}
	
	function set_included_objects($included_objects)
	{
		$this->included_objects = $included_objects;
	}
	
	function get_included_objects()
	{
		return $this->included_objects;
	}
	
	function add_included_object(ContentObject $object)
	{
		$this->included_objects[] = $object;
	}
	
}

?>