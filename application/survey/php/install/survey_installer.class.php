<?php
namespace application\survey;

use common\libraries\Installer;

require_once dirname(__FILE__) . '/../lib/survey_data_manager.class.php';


class SurveyInstaller extends Installer
{

    /**
     * Constructor
     */
    function SurveyInstaller($values)
    {
        parent :: __construct($values, SurveyDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>