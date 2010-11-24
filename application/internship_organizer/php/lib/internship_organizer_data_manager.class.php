<?php
namespace application\internship_organizer;

use common\libraries\DataManagerInterface;
use common\libraries\Configuration;
use common\libraries\Utilities;

class InternshipOrganizerDataManager implements DataManagerInterface
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
     * @return InternshipOrganizerDataManagerInterface The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '_internship_organizer_data_manager.class.php';
            $class = __NAMESPACE__ . '\\' . $type . 'InternshipOrganizerDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

}
?>