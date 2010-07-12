<?php
/**
 * Description of mediamosa_external_repository_data_managerclass
 *
 * @author jevdheyd
 */
class MediamosaExternalRepositoryDataManager{
     /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;
    
    /**
     * Constructor.
     */
    protected function PortfolioDataManager()
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
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_mediamosa_external_repository_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'MediamosaExternalRepositoryDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>
