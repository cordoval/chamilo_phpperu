<?php 
namespace application\metadata;
use common\libraries\Configuration;
/**
 *	This is a skeleton for a data manager for the Metadata Application.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *  @author Sven Vanpoucke
 *	@author Jens Vanderheyden
 */
abstract class MetadataDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->initialize();
	}

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return MetadataDataManager The data manager.
	 */
	static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_metadata_data_manager.class.php';
            $class = __NAMESPACE__ . '\\' . $type . 'MetadataDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

}
?>