<?php
/**
 *	This is a skeleton for a data manager for the Handbook Application.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *  
 *	@author Nathalie Blocry
 */
class HandbookDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Constructor.
	 */
	protected function HandbookDataManager()
	{
		$this->initialize();
	}

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return HandbookDataManager The data manager.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.Utilities :: camelcase_to_underscores($type).'.class.php';
			$class = $type.'HandbookDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}

	
}
?>