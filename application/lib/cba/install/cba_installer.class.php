<?php
require_once dirname(__FILE__).'/../cba_data_manager.class.php';

/**
 * @author Nick Van Loocke
 */
class CbaInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function CbaInstaller($values)
    {
    	parent :: __construct($values, CbaDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>