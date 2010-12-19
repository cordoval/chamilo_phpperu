<?php
namespace migration;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\MessageLogger;

use Exception;

/**
 * General migration properties class that describes the properties for a platform
 */

abstract class MigrationProperties
{
	/**
	 * @var MessageLogger $message_logger used to log messages
	 */
	private $message_logger;

	function __construct()
	{
		$this->message_logger = MessageLogger :: get_instance(__CLASS__);
	}

	/**
	 * Returns the message logger
	 * @return MessageLogger
	 */
	function get_message_logger()
	{
		return $this->message_logger;
	}

	function render_message()
	{
		return $this->message_logger->render();
	}

	/**
	 * Factory method for properties
	 * @param String $platform - the selected platform
	 * @return MigrationProperties
	 */
	function factory($platform)
	{
		$file = dirname(__FILE__) . '/platform/' . $platform . '/' . $platform . '_migration_properties.class.php';
		if(!file_exists($file))
		{
			throw new Exception(Translation :: get('CanNotFindMigrationProperties', array('PLATFORM' => $platform)));
		}

		require_once($file);

		$class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($platform) . 'MigrationProperties';

		return new $class();
	}

	abstract function validate_settings($settings, $selected_blocks, $migrated_blocks);
	abstract function get_migration_blocks();
}