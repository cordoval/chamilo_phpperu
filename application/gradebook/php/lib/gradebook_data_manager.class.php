<?php
/**
 * This abstract class provides the necessary functionality to connect a
 * gradebook to a storage system.
 */
class GradebookDataManager
{

    /**
     * Instance of the class, for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor. Initializes the data manager.
     */
    protected function GradebookDataManager()
    {
        $this->initialize();
    }

    /**
     * Creates the shared instance of the configured data manager if
     * necessary and returns it. Uses a factory pattern.
     * @return GradebookManager The instance.
     */
    static function get_instance()

    {

        if (! isset(self :: $instance))
        {

            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');

            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '_gradebook_data_manager.class.php';
            $class = $type . 'GradebookDataManager';

            self :: $instance = new $class();

        }
        return self :: $instance;
    }
}
?>