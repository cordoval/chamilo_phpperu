<?php
namespace application\handbook;
use common\libraries\Installer;
/**
 * handbook.install
 */

require_once dirname(__FILE__).'/../lib/handbook_data_manager.class.php';

/**
 * This installer can be used to create the storage structure for the
 * handbook application.
 *
 * @author Sven Vanpoucke
 * @author Nathalie Blocry
 */
class HandbookInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function __construct($values)
    {
    	parent :: __construct($values, HandbookDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>