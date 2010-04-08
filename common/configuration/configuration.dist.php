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
$configuration['database']['connection_string'] = '{DATABASE_DRIVER}://{DATABASE_USER}:{DATABASE_PASSWORD}@{DATABASE_HOST}/{DATABASE_NAME}';
$configuration['general']['root_web'] = '{ROOT_WEB}';
$configuration['general']['url_append'] = '{URL_APPEND}';
$configuration['general']['security_key'] = '{SECURITY_KEY}';
$configuration['general']['hashing_algorithm'] = '{HASHING_ALGORITHM}';
$configuration['general']['install_date'] = '{INSTALL_DATE}';
?>