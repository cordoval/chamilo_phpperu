<?php

namespace application\package;

use common\libraries\WebApplication;
use common\libraries\Installer;
use common\libraries\Translation;
use rights\RightsUtilities;
use common\libraries\Utilities;

/**
 * This installer can be used to create the storage structure for the
 * cda application.
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function __construct($values)
    {
    	parent :: __construct($values, PackageDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>