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
$configuration['general']['root_web'] = 'http://localhost/CHAT/';
$configuration['general']['url_append'] = '/Chamilo';
$configuration['general']['security_key'] = '7d65cb8092c58dcad4498ac275175e18';
$configuration['general']['hashing_algorithm'] = 'sha1';
?>
