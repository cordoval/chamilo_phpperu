<?php
/**
 * $Id: forum_data_manager.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum
 */
/**
 *	This is a skeleton for a data manager for the Forum Application.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Sven Vanpoucke & Michael Kyndt
 */
class ForumDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function ForumDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return ForumDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '_forum_data_manager.class.php';
            $class = $type . 'ForumDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

}
?>