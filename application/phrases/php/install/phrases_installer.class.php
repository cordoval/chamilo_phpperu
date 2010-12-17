<?php
namespace application\phrases;

use common\libraries\WebApplication;
use common\libraries\Installer;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesInstaller extends Installer
{

    /**
     * Constructor
     */
    function __construct($values)
    {
        parent :: __construct($values, PhrasesDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>