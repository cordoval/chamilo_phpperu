<?php 
namespace application\survey;

use common\libraries\Utilities;
use common\libraries\Configuration;
use common\libraries\EqualityCondition;
use common\libraries\DataManagerInterface;


class SurveyDataManager
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
     * @return SurveyDataManagerInterface The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '_survey_data_manager.class.php';
            $class = __NAMESPACE__.'\\'.$type . 'SurveyDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

}
?>