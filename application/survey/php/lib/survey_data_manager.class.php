<?php
/**
 * $Id: survey_data_manager.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey
 *
 * This is a skeleton for a data manager for the Survey Application.
 * Data managers must extend this class and implement its abstract methods.
 */
class SurveyDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function SurveyDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return SurveyDataManagerInterface The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '_survey_data_manager.class.php';
            $class = $type . 'SurveyDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

}
?>