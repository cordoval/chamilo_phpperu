<?php
namespace common\libraries;

abstract class TemplateCache
{
	static function factory($theme, $type)
	{
		$file = dirname(__file__) . '/template_cache/' . $type . '_template_cache.class.php';

		if(!file_exists($file))
			die('Could not load type ' . $type . ' as template cache');

		require_once($file);

		$classname = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'TemplateCache';
		return new $classname($theme);
	}

	abstract function cache($handle, $uncompiled_code, $compiled_code);
	abstract function retrieve_from_cache($handle, $uncompiled_code);
}

?>