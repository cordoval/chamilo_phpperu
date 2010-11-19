<?php
namespace application\metadata;
use common\libraries\Installer;

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
    function __construct($values)
    {
    	parent :: __construct($values, MetadataDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>