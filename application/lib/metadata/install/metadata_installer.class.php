<?php
/**
 * metadata.install
 */

require_once dirname(__FILE__).'/../metadata_data_manager.class.php';

/**
 * This installer can be used to create the storage structure for the
 * metadata application.
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function MetadataInstaller($values)
    {
    	parent :: __construct($values, MetadataDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>