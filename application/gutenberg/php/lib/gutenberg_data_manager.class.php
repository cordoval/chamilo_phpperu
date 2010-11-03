<?php
namespace application\gutenberg;

use common\libraries\Utilities;
use common\libraries\Configuration;

/**
 * $Id: gutenberg_data_manager.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.gutenberg
 */
class GutenbergDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function GutenbergDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return GutenbergDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_gutenberg_data_manager.class.php';
            $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'GutenbergDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

}
?>