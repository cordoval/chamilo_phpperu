<?php

class InternshipOrganizerDataManager implements DataManagerInterface
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function InternshipOrganizerDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return InternshipOrganizerDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
            $class = $type . 'InternshipOrganizerDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
   
}
?>