<?php

class SurveyContextDataManager implements DataManagerInterface
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

//    function __call($method, $args)
//    {
//        print "Method $method called:\n";
//        var_dump($args);
//        exit;
//    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return ContextDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/' . strtolower($type) . '_context_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'SurveyContextDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>