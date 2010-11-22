<?php
namespace application\cas_user;

use common\libraries\Installer;

/**
 * @author Hans De Bisschop
 */
class CasUserInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function __construct($values)
    {
    	parent :: __construct($values, CasUserDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>