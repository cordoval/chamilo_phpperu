<?php
namespace application\phrases;

use common\libraries\Configuration;
use common\libraries\Utilities;

/**
 * $Id: phrases_data_manager.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases
 */
/**
 * This is a skeleton for a data manager for the Phrases Application.
 * Data managers must extend this class and implement its abstract methods.
 *
 * @author Hans De Bisschop
 * @author
 */
class PhrasesDataManager
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
     * @return PhrasesDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_phrases_data_manager.class.php';
            $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'PhrasesDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

}
?>