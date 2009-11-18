<?php
/**
==============================================================================
 * This is the configuration file. You'll probably want to modify the values.
 * $Id: configuration.dist.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.configuration.html_editor
==============================================================================
 */
$configuration = array();
$configuration['general']['data_manager'] = 'Database';
$configuration['database']['connection_string'] = 'mysqli://root:@localhost/chamilo';
$configuration['general']['root_web'] = 'http://localhost/chamilo/';
$configuration['general']['url_append'] = '/chamilo';
$configuration['general']['security_key'] = '0ad30f12530011a1077275e9f66c498e';
$configuration['general']['hashing_algorithm'] = 'sha1';
?>
