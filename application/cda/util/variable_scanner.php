<?php

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once dirname(__FILE__) . '/variable_scanner/variable_scanner.class.php';

set_time_limit(0);

$root_path = Path :: get(SYS_PATH);
$root_app_path = $root_path . 'application/lib/';

$scanner = new VariableScanner();

$core_language_packs = array('admin', 'common', 'group', 'help', 'home', 'install', 'menu', 'migration', 'reporting',
						     'repository', 'rights', 'tracking', 'user', 'webservice');

foreach($core_language_packs as $core_language_pack)
{
	$scanner->scan_language_pack($root_path . $core_language_pack, $core_language_pack, LanguagePack :: TYPE_CORE);
}

$scanner->scan_language_pack($root_path . 'application/common', 'application_common', LanguagePack :: TYPE_CORE);

$optional_language_packs = array('alexia', 'assessment', 'cda', 'distribute', 'forum', 'laika', 'linker', 'personal_calendar',
						     'personal_messenger', 'portfolio', 'profiler', 'reservations', 'search_portal', 'webconferencing',
							 'weblcms', 'wiki');

foreach($optional_language_packs as $optional_language_pack)
{
	$scanner->scan_language_pack($root_app_path . $optional_language_pack, $optional_language_pack, LanguagePack :: TYPE_APPLICATION);
}

?>