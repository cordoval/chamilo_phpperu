<?php

require_once dirname(__FILE__) . '/../../global.inc.php';

$directory = Path :: get(SYS_PATH) . '';
$files = Filesystem :: get_directory_content($directory, Filesystem :: LIST_FILES, true);

$fh = fopen(dirname(__FILE__) . '/components.inc.php', 'w');
fwrite($fh, "<?php\n");

$regex = '/class [a-zA-Z0-9]*/';

$application = 'components';

foreach($files as $file)
{
	if(strpos($file, '.hg') != false)
	{
		continue;
	}
	
	$parts = explode('/', $file);
	if($parts[count($parts) - 2] == 'component')
	{
		$contents = file_get_contents($file);
		
		if(strpos($contents, 'DelegateComponent') != false)
		{
			continue;
		}
		
		preg_match($regex, $contents, $matches);
		$class = substr($matches[0], 6);
		
		fwrite($fh, '$lang[\'' . $application . '\'][\'' . $class . '\'] = ' . "'';\n");
	}
}

fwrite($fh, "?>");
fclose($fh);

?>