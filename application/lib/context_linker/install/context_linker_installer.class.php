<?php
/**
 * context_linker.install
 */

require_once dirname(__FILE__).'/../context_linker_data_manager.class.php';

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
    function ContextLinkerInstaller($values)
    {
    	parent :: __construct($values, ContextLinkerDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>