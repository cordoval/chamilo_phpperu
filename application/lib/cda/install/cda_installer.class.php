<?php
/**
 * cda.install
 */

require_once dirname(__FILE__).'/../cda_data_manager.class.php';

/**
 * This installer can be used to create the storage structure for the
 * cda application.
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function CdaInstaller($values)
    {
    	parent :: __construct($values, CdaDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>