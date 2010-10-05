<?php
/**
 * $Id: settings.inc.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.util.data_class_generator
 */

/**
 * Settings for dataclass generator
 */
$connectionstring = 'mysql://root:@localhost/';
$databases = array();
$databases[] = 'dokeos_main';
$databases[] = 'dokeos_user';
$databases[] = 'dokeos_stats';
$databases[] = 'DOKEOS';

$classprefix = 'Dokeos185';
$package = 'migration.lib.platform.dokeos185';
$author = 'Sven Vanpoucke';

/*$databases[] = 'dokeos_165_main';
$databases[] = 'dokeos_165_course';
$databases[] = 'dokeos_165_user';*/
?>