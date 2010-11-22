<?php
namespace application\context_linker;
use common\libraries\Installer;

/**
 * This installer can be used to create the storage structure for the
 * context_linker application.
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLinkerInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function __construct($values)
    {
    	parent :: __construct($values, ContextLinkerDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>