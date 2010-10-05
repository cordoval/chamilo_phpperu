<?php
/**
 *	This is a skeleton for a data manager for the Cda Application.
 *	Data managers must extend this class and implement its abstract methods.
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class CdaDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Constructor.
	 */
	protected function CdaDataManager()
	{
		$this->initialize();
	}

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return CdaDataManager The data manager.
	 */
	static function get_instance()
	{
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_cda_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'CdaDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
	}
}
?>