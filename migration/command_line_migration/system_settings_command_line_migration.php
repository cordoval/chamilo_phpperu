#!/usr/local/bin/php5
<?php
/**
 * $Id: system_settings_command_line_migration.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.command_line_migration
 */
/**
 * Start commandline migration
 */
ini_set('include_path', realpath(dirname(__FILE__) . '/../../plugin/pear'));
ini_set("memory_limit", "3500M"); // Geen php-beperkingen voor geheugengebruik
ini_set("max_execution_time", "72000"); // Twee uur moet voldoende zijn...


require_once dirname(__FILE__) . '/../../common/global.inc.php';
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once (dirname(__FILE__) . '/command_line_migration.class.php');
require_once (dirname(__FILE__) . '/../lib/migration_manager/component/inc/wizard/system_settings_migration_wizard_page.class.php');

require_once (dirname(__FILE__) . '/../lib/logger.class.php');

Translation :: set_application("migration");

$clm = new CommandLineMigration();
$wizardpage = new SystemSettingsMigrationWizardPage(null, null, true);
$clm->migrate($wizardpage);

unset($wizardpage);

?>