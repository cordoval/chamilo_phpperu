<?php
/**
 * $Id: assessment_data_manager.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment
 */
/**
 * This is a skeleton for a data manager for the Assessment Application.
 * Data managers must extend this class and implement its abstract methods.
 *
 * @author Sven Vanpoucke
 * @author
 */
class AssessmentDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function AssessmentDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return AssessmentDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_assessment_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'AssessmentDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

}
?>