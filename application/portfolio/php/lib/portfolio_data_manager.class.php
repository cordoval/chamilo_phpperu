<?php
namespace application\portfolio;
use common\libraries\Utilities;
use common\libraries\Configuration;


/**
 *	This is a skeleton for a data manager for the Portfolio Application.
 *	Data managers must extend this class and implement its methods.
 *
 *	@author Sven Vanpoucke
 */
class PortfolioDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function __construct()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return PortfolioDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_portfolio_data_manager.class.php';
            $class =  __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'PortfolioDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    


}
?>